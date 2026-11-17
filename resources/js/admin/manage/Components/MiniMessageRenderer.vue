<script setup lang="ts">
import type { MiniMessage as MiniMessageApi } from 'minimessage-js'
import miniMessageModuleUrl from 'minimessage-js/dist/minimessage.esm.js?url'
import { onMounted, ref, watch } from 'vue'

interface Props {
    text: string,
    placeholder: string,
    tag?: 'span' | 'div',
}

const props = withDefaults(defineProps<Props>(), {
    tag: 'div',
})

const preview = ref<HTMLElement>()
let miniMessage: MiniMessageApi | undefined

function renderPreview(): void {
    if (!preview.value) {
        return
    }

    preview.value.replaceChildren()

    if (!miniMessage) {
        preview.value.textContent = props.text || props.placeholder
        return
    }

    try {
        miniMessage.toHTML(miniMessage.deserialize(props.text || props.placeholder), preview.value)
    } catch {
        preview.value.textContent = props.text || props.placeholder
    }
}

onMounted(async () => {
    try {
        const { MiniMessage } = await import(/* @vite-ignore */ miniMessageModuleUrl)
        miniMessage = MiniMessage.miniMessage()
    } catch {
        miniMessage = undefined
    }

    renderPreview()
})

watch(() => props.text, renderPreview)
</script>

<template>
    <component :is="tag" ref="preview" />
</template>