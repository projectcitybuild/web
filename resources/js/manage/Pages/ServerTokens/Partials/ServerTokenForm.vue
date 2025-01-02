<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { computed, onMounted, ref } from 'vue'
import { Modal } from 'flowbite'
import { ServerToken } from '../../../Data/ServerToken'
import ErrorAlert from '../../../Components/ErrorAlert.vue'

interface Props {
    token?: ServerToken,
    submit: Function,
}

const props = defineProps<Props>()

const form = useForm<ServerToken>({
    description: props.token?.description,
    token: props.token?.token ?? generate(),
})

const deleteModal = ref()
const isEdit = computed(() => props.token != null)

onMounted(() => {
    const $modalEl = document.getElementById('deleteModal')
    const options = {
        placement: 'bottom-right',
        backdrop: 'dynamic',
        backdropClasses: 'bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40',
        closable: true,
    }
    deleteModal.value = new Modal($modalEl, options)
})

function generate() {
    const length = 32
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
    let result = ''
    const randomArray = new Uint8Array(length)
    crypto.getRandomValues(randomArray)
    randomArray.forEach((number) => {
        result += chars[number % chars.length]
    })
    return result
}

function submit() {
    props.submit(form)
}

function destroy() {
    deleteModal.value.hide()
    form.delete('/manage/server-tokens/' + props.token.id)
}
</script>

<template>
    <form @submit.prevent="submit">
        <ErrorAlert v-if="form.hasErrors" :errors="form.errors" class="mb-4"/>

        <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
            <div class="col-span-2">
                <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Description<span class="text-red-500">*</span>
                </label>
                <input
                    v-model="form.description"
                    id="name"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="For PCBridge"
                >
                <span class="block mt-2 text-xs font-medium text-gray-400 dark:text-white">
                    Write a description that notes what is consuming the token (eg. PCBridge)
                </span>
                <div v-if="form.errors.description" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.description }}
                </div>
            </div>
            <div class="col-span-2">
                <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Access Token<span class="text-red-500">*</span>
                </label>
                <div class="flex flex-row gap-2">
                    <input
                        v-model="form.token"
                        id="token"
                        class="grow p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    >
                    <button
                        type="button"
                        class="rounded-lg bg-gray-500 text-white p-2 hover:bg-gray-600 focus:bg-gray-800"
                        @click="form.token = generate()"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                        </svg>
                    </button>
                </div>
                <div v-if="form.errors.token" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.token }}
                </div>
            </div>
        </div>

        <div class="flex flex-row gap-2">
            <button
                :disabled="form.processing"
                type="submit"
                class="px-5 py-2.5 mt-4 sm:mt-6 text-sm text-center text-white bg-blue-700 rounded-lg focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900 hover:bg-blue-800"
            >
                {{ isEdit ? 'Update' : 'Create' }}
            </button>

            <button
                v-if="isEdit"
                type="button"
                class="
                    px-5 py-2.5 mt-4 sm:mt-6
                    text-sm text-center text-red-500 border border-red-500 rounded-lg
                    focus:ring-4 focus:ring-red-200
                    hover:bg-red-50
                    dark:focus:ring-red-900
                "
                @click="deleteModal.show"
            >
                Delete
            </button>
        </div>

        <div id="deleteModal" tabindex="-1"
             class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <div class="p-4 md:p-8 text-center">
                        <h3 class="mb-5 text-2xl font-bold">
                            Are you sure?
                        </h3>
                        <div class="text-sm mb-8 text-gray-500">
                            This action cannot be undone
                        </div>
                        <button
                            type="button"
                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center"
                            @click="destroy"
                        >
                            Yes, Delete It
                        </button>
                        <button
                            type="button"
                            class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                            @click="deleteModal.hide"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>
