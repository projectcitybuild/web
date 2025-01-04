<script setup lang="ts">
import { onMounted, ref, useId } from 'vue'
import FilledButton from './FilledButton.vue'
import OutlinedButton from './OutlinedButton.vue'

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

            <h1 class="mt-6 text-xl font-bold">
                {{ title ?? 'Are You Sure?' }}
            </h1>

            <div class="mt-4 text-sm text-gray-500 max-w-96">
                {{ message }}
            </div>
        </div>

        <div class="px-8 py-6 flex flex-col md:flex-row justify-stretch md:justify-stretch gap-2 bg-gray-50">
            <OutlinedButton
                variant="secondary"
                class="grow"
                @click="close"
            >
                Cancel
            </OutlinedButton>

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
