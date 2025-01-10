<script setup lang="ts">
import { onMounted, ref, useId } from 'vue'

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
        <slot />
    </dialog>
</template>
