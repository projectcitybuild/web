<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import { onMounted, ref, watch } from 'vue'
import { watchDebounced } from '@vueuse/core'
import { format } from '../../Utilities/DateFormatter'
import type { RemoteConfig } from '../../Data/RemoteConfig'
import ErrorAlert from '../../Components/ErrorAlert.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import JsonValidity from './Partials/JsonValidity.vue'
import Card from '../../Components/Card.vue'
import FilledButton from '../../Components/FilledButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'

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
</script>

<template>
    <Head title="Minecraft Config"/>

    <SuccessAlert v-if="success" :message="success" class="mb-4"/>

    <section>
        <Card>
            <div
                class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                <div class="w-full md:w-1/2">
                    <h1 class="text-xl">Version {{ config.version }}</h1>
                    <span class="text-xs text-gray-500">
                        (Last edited: {{ format(config.updated_at, 'yyyy/MM/dd h:ma') }})</span>
                </div>
                <div>
                    <FilledButton
                        variant="primary"
                        :disabled="!isValidJson"
                        @click="submit"
                    >
                        <SvgIcon icon="cloud-push" />
                        Save & Deploy
                    </FilledButton>
                </div>
            </div>
            <div class="p-4">
                <ErrorAlert v-if="form.hasErrors" :errors="form.errors" class="mb-4"/>

                <JsonValidity
                    v-if="isValidJson != null"
                    :is-valid="isValidJson"
                    :is-parsing="isWaiting"
                />

                <form @submit.prevent="submit">
                    <textarea
                        v-model="form.config"
                        id="config"
                        rows="18"
                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="{}"
                    ></textarea>
                </form>
            </div>
        </Card>
    </section>
</template>
