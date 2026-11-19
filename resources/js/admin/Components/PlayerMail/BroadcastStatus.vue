<script setup lang="ts">
import Pill from '../Pill.vue'

interface Props {
    status: string,
}

defineProps<Props>()

const labels: Record<string, string> = {
    queued: 'Pending',
    processing: 'Processing',
    completed: 'Completed',
    failed: 'Failed',
}

const variants: Record<string, 'warning' | 'info' | 'success' | 'danger'> = {
    queued: 'warning',
    processing: 'info',
    completed: 'success',
    failed: 'danger',
}
</script>

<template>
    <Pill :variant="variants[status] ?? 'default'" class="whitespace-nowrap">
        {{ labels[status] ?? status }}
    </Pill>
</template>
