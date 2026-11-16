<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { computed, onMounted, ref, watch } from 'vue'
import { watchDebounced } from '@vueuse/core'
import { format } from '../../Utilities/DateFormatter'
import type { RemoteConfig } from '../../Data/RemoteConfig'
import ErrorAlert from '../../Components/ErrorAlert.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import JsonValidity from './Partials/JsonValidity.vue'
import JsonEditor from './Partials/JsonEditor.vue'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import FilledButton from '../../Components/FilledButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import { Icons } from '../../Icons'
import OutlinedButton from '../../Components/OutlinedButton.vue'
import MiniMessageRenderer from '../../Components/MiniMessageRenderer.vue'

interface MessageEntry {
    key: string,
    label: string,
    value: string,
}

interface Props {
    success?: string,
    config: RemoteConfig,
}

const props = defineProps<Props>()
const form = useForm({
    config: JSON.stringify(props.config.config, null, 2),
})

const isValidJson = ref<boolean | null>(null)
const isWaiting = ref(false)
const lineCount = computed(() => form.config.split('\n').length)
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

function submit() {
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
        title="Minecraft Config"
        wide
        :description="'Version ' + config.version + ' - Last edited: ' + format(config.updated_at, 'yyyy/MM/dd h:ma')"
    >
        <template #actions>
                    <FilledButton
                        variant="primary"
                        :disabled="!isValidJson"
                        @click="submit"
                    >
                        <SvgIcon :svg="Icons.cloudPush" />
                        Save & Deploy
                    </FilledButton>
        </template>

        <SuccessAlert v-if="success" :message="success" />
        <ErrorAlert v-if="form.hasErrors" :errors="form.errors" />

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.6fr)_minmax(20rem,1fr)]">
            <section class="min-w-0 space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 pb-3 dark:border-gray-700">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">Configuration</span>
                    <JsonValidity
                        v-if="isValidJson !== null"
                        :is-valid="isValidJson"
                        :is-parsing="isWaiting"
                    />
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-xs tabular-nums text-gray-500 dark:text-gray-400">{{ lineCount }} lines</span>
                    <OutlinedButton
                        type="button"
                        class="text-sm font-medium text-primary-700 hover:text-primary-900 disabled:cursor-not-allowed disabled:opacity-50 dark:text-primary-300 dark:hover:text-primary-200"
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
                        aria-label="Minecraft configuration JSON"
                        class="rounded-lg border border-gray-300 bg-slate-50 shadow-inner focus-within:ring-2 focus-within:ring-primary-500/30 dark:border-gray-600 dark:bg-gray-900"
                        @select-keys="selectPreviewKeys"
                    />
                </form>

            </section>

            <section class="min-w-0 space-y-3 border-t border-gray-200 pt-4 dark:border-gray-700 xl:border-l xl:border-t-0 xl:pl-6 xl:pt-0">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">MiniMessage preview</h2>
                </div>
                <div class="min-h-20 break-words border border-gray-700 bg-gray-900 p-4 font-mono text-sm text-gray-100">
                    <MiniMessageRenderer
                        :text="selectedPreviewText"
                        placeholder="Place the cursor on a string key or select lines with string values to preview."
                        class="whitespace-pre-wrap"
                    />
                </div>
                <span v-if="selectedEntryLabels" class="break-all font-mono text-xs text-gray-500 dark:text-gray-400">{{ selectedEntryLabels }}</span>
            </section>
        </div>
    </ManageFormPage>
</template>
