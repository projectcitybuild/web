<script setup lang="ts">
import { computed, onBeforeUnmount, ref } from 'vue'
import Button from './Button.vue'
import SvgIcon from './SvgIcon.vue'
import { Icons } from '../Components/SvgIcon.vue'

interface Props {
    text: string | null,
    label: string,
}

const props = defineProps<Props>()
const copied = ref(false)
const error = ref<string|null>(null)
let copiedTimeout: ReturnType<typeof setTimeout> | undefined

const canCopy = computed(() => props.text !== null && props.text.trim() !== '')

onBeforeUnmount(() => {
    if (copiedTimeout) {
        clearTimeout(copiedTimeout)
    }
})

async function copyText(): Promise<void> {
    if (copiedTimeout) {
        clearTimeout(copiedTimeout)
        copiedTimeout = undefined
    }

    copied.value = false
    error.value = null

    const text = props.text

    if (text === null || text.trim() === '') {
        return
    }

    try {
        await navigator.clipboard.writeText(text)
        copied.value = true
        copiedTimeout = setTimeout(() => {
            copied.value = false
            copiedTimeout = undefined
        }, 1500)
    } catch {
        error.value = `Unable to copy ${props.label}. Check clipboard permissions and try again.`
    }
}
</script>

<template>
    <span class="inline-flex items-center justify-end gap-1.5">
        <span v-if="error" class="text-xs text-danger" role="alert" :title="error">Copy failed</span>
        <span class="relative inline-flex">
            <span
                v-if="copied"
                class="copy-feedback pointer-events-none absolute bottom-full left-1/2 z-10 mb-1 -translate-x-1/2 whitespace-nowrap rounded-md bg-success px-2 py-1 text-xs font-medium text-success-foreground shadow-sm"
                role="status"
            >
                Copied
            </span>
            <Button
                variant="outline"
                size="sm"
                :icon-only="true"
                :aria-label="`Copy ${props.label}`"
                :title="`Copy ${props.label}`"
                :disabled="!canCopy"
                @click="copyText"
            >
                <SvgIcon :svg="copied ? Icons.check : Icons.copy" class="size-4" aria-hidden="true" />
            </Button>
        </span>
    </span>
</template>

<style scoped>
.copy-feedback {
    animation: copy-feedback 700ms ease-out forwards;
}

@keyframes copy-feedback {
    0% {
        opacity: 0;
        transform: translate(-50%, 4px);
    }

    20% {
        opacity: 1;
        transform: translate(-50%, 0);
    }

    100% {
        opacity: 0;
        transform: translate(-50%, -10px);
    }
}

@media (prefers-reduced-motion: reduce) {
    .copy-feedback {
        animation: none;
        transform: translateX(-50%);
    }
}
</style>
