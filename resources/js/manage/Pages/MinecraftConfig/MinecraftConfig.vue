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

        <div class="space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 pb-3 dark:border-gray-700">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">Configuration</span>
                    <span class="rounded bg-gray-100 px-2 py-0.5 font-mono text-xs text-gray-600 dark:bg-gray-700 dark:text-gray-300">JSON</span>
                    <span
                        v-if="isValidJson === false && !isWaiting"
                        class="rounded-full border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 dark:border-red-800 dark:bg-red-950/50 dark:text-red-300"
                    >
                        Invalid JSON
                    </span>
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
                    class="rounded-lg border border-gray-300 bg-slate-50 shadow-inner focus-within:ring-2 focus-within:ring-primary-500/30 dark:border-gray-600"
                />
            </form>

            <JsonValidity
                v-if="isValidJson === true"
                :is-valid="isValidJson"
                :is-parsing="isWaiting"
            />
        </div>
    </ManageFormPage>
</template>
