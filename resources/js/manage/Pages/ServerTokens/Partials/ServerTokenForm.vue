<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { ServerToken } from '../../../Data/ServerToken'
import ErrorAlert from '../../../Components/ErrorAlert.vue'
import FilledButton from '../../../Components/FilledButton.vue'
import Spinner from '../../../Components/Spinner.vue'
import ConfirmDialog from '../../../Components/ConfirmDialog.vue'
import SvgIcon from '../../../Components/SvgIcon.vue'
import OutlinedButton from '../../../Components/OutlinedButton.vue'

interface Props {
    token?: ServerToken,
    submit: Function,
}

const props = defineProps<Props>()

const form = useForm<ServerToken>({
    description: props.token?.description,
    token: props.token?.token ?? generate(),
})

const deleteModal = ref<InstanceType<typeof ConfirmDialog>>()
const isEdit = computed(() => props.token != null)

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
    deleteModal?.value.close()
    form.delete('/manage/server-tokens/' + props.token.id)
}
</script>

<template>
    <form @submit.prevent="submit">
        <ConfirmDialog
            ref="deleteModal"
            message="This cannot be undone."
            confirm-title="Yes, Delete It"
            :on-confirm="destroy"
        />

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
                    <FilledButton
                        variant="danger"
                        @click="form.token = generate()"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                        </svg>
                    </FilledButton>
                </div>
                <div v-if="form.errors.token" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.token }}
                </div>
            </div>
        </div>

        <div class="flex flex-row gap-2 mt-8">
            <FilledButton
                variant="primary"
                :disabled="form.processing"
            >
                <Spinner v-if="form.processing" />
                <template v-else>{{ isEdit ? 'Update' : 'Create' }}</template>
            </FilledButton>

            <OutlinedButton
                variant="danger"
                v-if="isEdit"
                type="button"
                @click="deleteModal.open()"
            >
                <SvgIcon icon="bin" />
                Delete
            </OutlinedButton>
        </div>
    </form>
</template>
