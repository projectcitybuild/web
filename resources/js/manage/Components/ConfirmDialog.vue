<script setup lang="ts">
import { onMounted, ref, useId } from 'vue'
import FilledButton from './FilledButton.vue'

interface Props {
    title?: string,
    message: string,
    onConfirm: () => void,
    confirmTitle?: string,
}
defineProps<Props>()

defineExpose({
    open: open,
    close: close,
})

const dialog = ref<HTMLDialogElement>()
const id = useId()

function open() {
    dialog.value?.showModal()
}

function close() {
    dialog.value?.close()
}

onMounted(() => {
    const element = document.getElementById(id)

    element!.addEventListener('click', (event) => {
        const rect = element!.getBoundingClientRect()
        const isInDialog = rect.top <= event.clientY
            && event.clientY <= rect.top + rect.height
            && event.clientX > rect.left
            && event.clientX <= rect.left + rect.width

        if (!isInDialog) close()
    })
})
</script>

<template>
    <dialog
        :id="id"
        ref="dialog"
        class="rounded-lg shadow-xl border border-gray-300"
    >
        <div class="p-8 flex flex-col justify-start">
            <svg class="size-16 bg-red-50 p-4 rounded-full text-red-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>

            <h1 class="mt-4 text-xl font-bold">
                {{ title ?? 'Are You Sure?' }}
            </h1>

            <div class="mt-4 text-md text-gray-500 max-w-96">
                {{ message }}
            </div>
        </div>

        <div class="px-8 py-6 flex flex-col md:flex-row justify-stretch md:justify-stretch gap-2 bg-gray-50">
            <button
                type="button"
                class="grow py-2.5 px-5 text-sm text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                @click="close"
            >
                Cancel
            </button>

            <FilledButton
                variant="danger"
                @click="onConfirm"
                class="grow"
            >
                {{ confirmTitle ?? 'Continue' }}
            </FilledButton>
        </div>
    </dialog>
</template>
