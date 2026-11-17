<script setup lang="ts">
import MiniMessageRenderer from '../../../Components/MiniMessageRenderer.vue'

interface Props {
    subject: string,
    body: string,
}

const props = defineProps<Props>()
</script>

<template>
    <article class="overflow-hidden border border-gray-700 bg-gray-900 text-gray-100">
        <div class="flex items-center justify-between border-b border-gray-700 px-4 py-3 text-xs font-semibold uppercase text-emerald-300">
            <span>Project City Build</span>
            <span>Announcement</span>
        </div>
        <div class="p-5 font-mono">
            <h3 class="mb-3 break-words text-base font-semibold text-white">
                <MiniMessageRenderer tag="span" :text="props.subject" placeholder="Announcement subject" />
            </h3>
            <MiniMessageRenderer
                :text="props.body"
                placeholder="Your announcement message will appear here."
                class="min-h-24 whitespace-pre-wrap break-words text-sm leading-6 text-gray-200"
            />
        </div>
    </article>
</template>
