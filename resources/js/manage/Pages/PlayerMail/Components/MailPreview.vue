<script setup lang="ts">
import type { MiniMessage as MiniMessageApi } from 'minimessage-js'
import miniMessageModuleUrl from 'minimessage-js/dist/minimessage.esm.js?url'
import { onMounted, ref, watch } from 'vue'

interface Props {
    subject: string,
    body: string,
}

const props = defineProps<Props>()
const subjectPreview = ref<HTMLElement>()
const bodyPreview = ref<HTMLElement>()
let miniMessage: MiniMessageApi | undefined

function renderText(target: HTMLElement | undefined, text: string, placeholder: string): void {
    if (!target) {
        return
    }

    target.replaceChildren()

    if (!miniMessage) {
        target.textContent = text || placeholder
        return
    }

    try {
        miniMessage.toHTML(miniMessage.deserialize(text || placeholder), target)
    } catch {
        target.textContent = text || placeholder
    }
}

function renderPreview(): void {
    renderText(subjectPreview.value, props.subject, 'Announcement subject')
    renderText(bodyPreview.value, props.body, 'Your announcement message will appear here.')
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
watch(() => [props.subject, props.body], renderPreview)
</script>

<template>
    <article class="overflow-hidden border border-gray-700 bg-gray-900 text-gray-100">
        <div class="flex items-center justify-between border-b border-gray-700 px-4 py-3 text-xs font-semibold uppercase text-emerald-300">
            <span>Project City Build</span>
            <span>Announcement</span>
        </div>
        <div class="p-5 font-mono">
            <h3 class="mb-3 break-words text-base font-bold text-white">
                <span ref="subjectPreview" />
            </h3>
            <div ref="bodyPreview" class="min-h-24 whitespace-pre-wrap break-words text-sm leading-6 text-gray-200" />
        </div>
    </article>
</template>
