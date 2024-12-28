<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import { onMounted, ref, watch } from 'vue'
import { watchDebounced } from '@vueuse/core'
import { format } from '../../Utilities/DateFormatter'
import type { RemoteConfig } from '../../Data/RemoteConfig'
import ErrorAlert from "../../Components/ErrorAlert.vue";
import SuccessAlert from "../../Components/SuccessAlert.vue";
import JsonValidity from "./Partials/JsonValidity.vue";

interface Props {
    success?: string,
    config: RemoteConfig,
}
const props = defineProps<Props>()
const form = useForm({
    config: JSON.stringify(props.config.config, null, 2),
})

const isValidJson = ref<boolean|null>(null)
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
    { debounce: 300, maxWait: 1000 }
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
    })).patch('/manage/minecraft/config', {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Minecraft Config" />

    <section class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5">
        <div class="mx-auto max-w-screen-xl">
            <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                    <div class="w-full md:w-1/2">
                        <h1 class="text-xl">Version {{ config.version }}</h1>
                        <span class="text-xs text-gray-500">(Last edited: {{ format(config.updated_at, 'yyyy/MM/dd h:ma') }})</span>
                    </div>
                    <div>
                        <button
                            class="
                                flex flex-row items-center gap-2 justify-center px-4 py-2 rounded-lg
                                text-sm text-white bg-blue-700
                                hover:bg-primary-800
                                focus:ring-4 focus:ring-blue-300
                                dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800
                            "
                            :disabled="!isValidJson"
                            @click="submit"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
                            </svg>
                            Save & Deploy
                        </button>
                    </div>
                </div>
                <div class="p-4">
                    <SuccessAlert v-if="success" :message="success" class="mb-4" />
                    <ErrorAlert v-if="form.hasErrors" :errors="form.errors" class="mb-4" />

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
            </div>
        </div>
    </section>
</template>
