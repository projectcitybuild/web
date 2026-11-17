<script setup lang="ts">
import { computed, onBeforeUnmount, ref } from 'vue'
import Button from '../../shared/Components/Button.vue'
import SvgIcon from './SvgIcon.vue'
import { Icons } from '../Icons'

interface Props {
    x: number|string|null,
    y: number|string|null,
    z: number|string|null,
    yaw: number|string|null,
    pitch: number|string|null,
}

const props = defineProps<Props>()
const copied = ref(false)
const error = ref<string|null>(null)
let copiedTimeout: ReturnType<typeof setTimeout> | undefined

const coordinates = computed(() => {
    const values = [props.x, props.y, props.z, props.yaw, props.pitch].map((value) => {
        if (value === null) {
            return null
        }

        const coordinate = String(value).trim()

        return coordinate !== '' && Number.isFinite(Number(coordinate))
            ? coordinate
            : null
    })

    return values.every((value) => value !== null) ? values.join(', ') : null
})

onBeforeUnmount(() => {
    if (copiedTimeout) {
        clearTimeout(copiedTimeout)
    }
})

async function copyLocation(): Promise<void> {
    if (copiedTimeout) {
        clearTimeout(copiedTimeout)
        copiedTimeout = undefined
    }

    copied.value = false
    error.value = null

    if (coordinates.value === null) {
        return
    }

    try {
        await navigator.clipboard.writeText(coordinates.value)
        copied.value = true
        copiedTimeout = setTimeout(() => {
            copied.value = false
            copiedTimeout = undefined
        }, 1500)
    } catch {
        error.value = 'Unable to copy coordinates. Check clipboard permissions and try again.'
    }
}
</script>

<template>
    <span class="inline-flex items-center justify-end gap-1.5">
        <span v-if="error" class="text-xs text-danger" role="alert">Copy failed</span>
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
                aria-label="Copy location coordinates"
                title="Copy location coordinates"
                :disabled="coordinates === null"
                @click="copyLocation"
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
