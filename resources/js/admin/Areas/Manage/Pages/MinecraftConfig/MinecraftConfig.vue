<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { computed, onMounted, ref, watch } from 'vue'
import { watchDebounced } from '@vueuse/core'
import { format } from '../../../../Utilities/DateFormatter'
import SuccessAlert from '../../../../Components/SuccessAlert.vue'
import JsonValidity from './Partials/JsonValidity.vue'
import JsonEditor from './Partials/JsonEditor.vue'
import ManageFormPage from '../../../../Components/ManageFormPage.vue'
import FilledButton from '../../../../Components/FilledButton.vue'
import SvgIcon from '../../../../Components/SvgIcon.vue'
import { Icons } from '../../../../Navigation/Icons'
import OutlinedButton from '../../../../Components/OutlinedButton.vue'
import MiniMessageRenderer from '../../../../Components/MiniMessageRenderer.vue'
import ConfirmDialog from '../../../../Components/ConfirmDialog.vue'
import FormErrorSummary from '../../../../Components/Forms/FormErrorSummary.vue'
import FormNotice from '../../../../Components/Forms/FormNotice.vue'
import { useUnsavedChangesWarning } from '../../../../Components/Forms/useUnsavedChangesWarning'

interface MessageEntry {
    key: string,
    label: string,
    value: string,
}

const props = defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/MinecraftConfig/MinecraftConfig']>()
const form = useForm({
    config: JSON.stringify(props.config.config, null, 2),
})

const isValidJson = ref<boolean | null>(null)
const isWaiting = ref(false)
const deploymentDialog = ref<InstanceType<typeof ConfirmDialog>>()
const lineCount = computed(() => form.config.split('\n').length)
const canDeploy = computed(() => isValidJson.value === true && !isWaiting.value && !form.processing)
const messageEntries = computed(() => {
    try {
        return collectMessageEntries(JSON.parse(form.config))
    } catch {
        return []
    }
})
const selectedKeys = ref<string[]>([])
const selectedEntries = computed(() => selectedKeys.value.flatMap((key) => {
    const entry = messageEntries.value.find((candidate) => candidate.key === key)
    return entry ? [entry] : []
}))
const selectedEntryLabels = computed(() => selectedEntries.value.map((entry) => entry.label).join(', '))
const selectedPreviewText = computed(() => selectedEntries.value.map((entry) => entry.value).join('\n'))

watch(messageEntries, (entries) => {
    const availableKeys = new Set(entries.map((entry) => entry.key))
    selectedKeys.value = selectedKeys.value.filter((key) => availableKeys.has(key))

    if (selectedKeys.value.length === 0 && entries.length > 0) {
        selectedKeys.value = [entries[0].key]
    }
}, {immediate: true})

watch(
    () => form.config,
    () => isWaiting.value = true,
)
watchDebounced(
    () => form.config,
    () => {
        isWaiting.value = false
        isValidJson.value = validateJson(form.config)
    },
    {debounce: 300, maxWait: 1000}
)

onMounted(() => isValidJson.value = validateJson(form.config))
useUnsavedChangesWarning(form)

function validateJson(json: string) {
    try {
        JSON.parse(json)
        return true
    } catch (e) {
        return false
    }
}

function collectMessageEntries(value: unknown, path: string[] = []): MessageEntry[] {
    if (typeof value === 'string') {
        if (path.length === 0) {
            return []
        }

        return [{ key: JSON.stringify(path), label: path.join('.'), value }]
    }

    if (Array.isArray(value)) {
        return value.flatMap((item, index) => collectMessageEntries(item, [...path, String(index)]))
    }

    if (value !== null && typeof value === 'object') {
        return Object.entries(value).flatMap(([key, item]) => collectMessageEntries(item, [...path, key]))
    }

    return []
}

function selectPreviewKeys(keys: string[]) {
    const availableKeys = new Set(messageEntries.value.map((entry) => entry.key))
    const matchingKeys = keys.filter((key) => availableKeys.has(key))

    if (matchingKeys.length > 0) {
        selectedKeys.value = matchingKeys
    }
}

function confirmDeployment(): void {
    deploymentDialog.value?.open()
}

function submit(): void {
    if (!canDeploy.value) {
        return
    }

    deploymentDialog.value?.close()
    form.transform((data) => ({
        ...data,
        config: JSON.stringify(JSON.parse(data.config)),
    })).post('/manage/minecraft/config')
}

function formatConfig() {
    if (!isValidJson.value) {
        return
    }

    form.config = JSON.stringify(JSON.parse(form.config), null, 2)
}
</script>

<template>
    <ManageFormPage
        title="Remote Config"
        wide
        :description="'Review and deploy the Minecraft server configuration. Version ' + config.version + ' - Last edited: ' + format(config.updated_at)"
    >
        <template #actions>
            <FilledButton
                variant="primary"
                :loading="form.processing"
                :disabled="!canDeploy"
                @click="confirmDeployment"
            >
                <SvgIcon :svg="Icons.cloudPush" />
                Save & Deploy
            </FilledButton>
        </template>

        <SuccessAlert v-if="success" :message="success" />
        <SuccessAlert v-else-if="form.recentlySuccessful" message="Configuration saved and deployed successfully." />
        <FormErrorSummary :errors="form.errors" />
        <FormNotice v-if="isWaiting" type="info" title="Checking JSON">
            Validation will update when you pause editing.
        </FormNotice>
        <FormNotice v-else-if="isValidJson === false" type="danger" title="JSON is invalid">
            Fix the syntax errors before this configuration can be deployed.
        </FormNotice>
        <FormNotice v-else-if="form.isDirty" type="warning" title="Valid JSON, not yet deployed">
            The configuration is syntactically valid, but your changes have not been deployed.
        </FormNotice>
        <ConfirmDialog
            ref="deploymentDialog"
            title="Deploy remote configuration?"
            message="This saves the current JSON and sends the configuration to the Minecraft server."
            confirm-title="Save and deploy"
            confirm-variant="primary"
            :on-confirm="submit"
        />

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.6fr)_minmax(20rem,1fr)]">
            <section class="min-w-0 space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-border pb-3">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold text-foreground dark:text-muted">Configuration</span>
                    <JsonValidity
                        v-if="isValidJson !== null"
                        :is-valid="isValidJson"
                        :is-parsing="isWaiting"
                    />
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-xs tabular-nums text-muted dark:text-subtle">{{ lineCount }} lines</span>
                    <OutlinedButton
                    <OutlinedButton
                        type="button"
                        variant="secondary"
                        class="text-sm font-medium text-primary hover:text-primary-hover disabled:cursor-not-allowed disabled:opacity-50 dark:hover:text-primary-200"
                        :disabled="!isValidJson"
                        @click="formatConfig"
                    >
                        Format JSON
                    </OutlinedButton>
                </div>
                </div>

                <form @submit.prevent="submit">
                    <JsonEditor
                        v-model="form.config"
                        ariaLabel="Minecraft configuration JSON"
                        :aria-invalid="form.errors.config ? 'true' : undefined"
                        class="rounded-lg border border-border-strong bg-surface-muted shadow-inner focus-within:ring-2 focus-within:ring-focus/30"
                        @select-keys="selectPreviewKeys"
                    />
                </form>

            </section>

            <section class="min-w-0 space-y-3 border-t border-border pt-4 xl:border-l xl:border-t-0 xl:pl-6 xl:pt-0">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-sm font-semibold text-foreground dark:text-muted">MiniMessage preview</h2>
                </div>
                <div class="min-h-20 break-words border border-border bg-gray-900 p-4 font-mono text-sm text-muted">
                    <MiniMessageRenderer
                        :text="selectedPreviewText"
                        placeholder="Place the cursor on a string key or select lines with string values to preview."
                        class="whitespace-pre-wrap"
                    />
                </div>
                <span v-if="selectedEntryLabels" class="break-all font-mono text-xs text-muted dark:text-subtle">{{ selectedEntryLabels }}</span>
            </section>
        </div>
    </ManageFormPage>
</template>
