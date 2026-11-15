<script setup lang="ts">
import { onMounted, ref, useId } from 'vue'
import FilledButton from './FilledButton.vue'
import OutlinedButton from './OutlinedButton.vue'

interface Props {
    title?: string,
    message: string,
    onConfirm: () => void,
    confirmTitle?: string,
    confirmVariant?: 'primary' | 'danger',
}
withDefaults(defineProps<Props>(), {
    confirmVariant: 'danger',
})

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
        :aria-labelledby="`${id}-title`"
        :aria-describedby="`${id}-description`"
        class="m-auto max-h-[calc(100dvh-2rem)] w-[min(32rem,calc(100%-2rem))] overflow-y-auto rounded-lg border border-border bg-surface p-0 text-foreground shadow-xl backdrop:bg-black/50"
    >
        <div class="p-8 flex flex-col justify-start">
            <svg aria-hidden="true" class="size-16 rounded-full bg-surface-muted p-4 text-danger" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>

            <h1 :id="`${id}-title`" class="mt-6 text-xl font-bold">
                {{ title ?? 'Are You Sure?' }}
            </h1>

            <div :id="`${id}-description`" class="mt-4 max-w-96 break-words text-sm text-muted">
                {{ message }}
            </div>
        </div>

        <div class="flex flex-col justify-stretch gap-2 bg-surface-muted px-8 py-6 md:flex-row md:justify-stretch">
            <OutlinedButton
                variant="secondary"
                class="grow"
                @click="close"
            >
                Cancel
            </OutlinedButton>

            <FilledButton
                :variant="confirmVariant"
                @click="onConfirm"
                class="grow"
            >
                {{ confirmTitle ?? 'Continue' }}
            </FilledButton>
        </div>
    </dialog>
</template>
