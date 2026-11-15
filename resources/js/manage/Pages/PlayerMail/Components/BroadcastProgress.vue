<script setup lang="ts">
import { computed } from 'vue'

interface Props {
    delivered: number,
    audienceSize: number,
    status: string,
}

const props = defineProps<Props>()

const percentage = computed(() => {
    if (props.status === 'completed') {
        return 100
    }

    if (props.audienceSize === 0) {
        return 0
    }

    return Math.min(100, Math.round((props.delivered / props.audienceSize) * 100))
})

const summary = computed(() => {
    if (props.audienceSize === 0) {
        return props.status === 'completed'
            ? 'Completed with no players in the captured audience.'
            : `${props.delivered} players delivered; the captured audience is empty.`
    }

    return `${props.delivered} of approximately ${props.audienceSize} players delivered`
})
</script>

<template>
    <div>
        <div class="mb-2 flex flex-wrap items-baseline justify-between gap-2 text-sm">
            <span class="font-medium text-gray-900 dark:text-white">{{ summary }}</span>
            <span v-if="audienceSize > 0" class="text-gray-500">{{ percentage }}%</span>
        </div>
        <div
            v-if="audienceSize > 0"
            class="h-2 overflow-hidden bg-gray-200 dark:bg-gray-700"
            role="progressbar"
            :aria-valuenow="percentage"
            aria-valuemin="0"
            aria-valuemax="100"
            :aria-label="summary"
        >
            <div
                class="h-full transition-[width] duration-300"
                :class="status === 'failed' ? 'bg-red-600' : status === 'completed' ? 'bg-emerald-600' : 'bg-sky-600'"
                :style="{ width: `${percentage}%` }"
            />
        </div>
        <p v-if="status === 'failed'" class="mt-2 text-sm font-medium text-red-700 dark:text-red-300">
            Delivery stopped before the announcement reached its full audience.
        </p>
    </div>
</template>
