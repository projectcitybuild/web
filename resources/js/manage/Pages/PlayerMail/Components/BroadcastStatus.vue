<script setup lang="ts">
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

const styles: Record<string, string> = {
    queued: 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200',
    processing: 'bg-sky-100 text-sky-800 dark:bg-sky-900 dark:text-sky-200',
    completed: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200',
    failed: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
}
</script>

<template>
    <span
        class="inline-flex whitespace-nowrap px-2.5 py-1 text-xs font-semibold"
        :class="styles[status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200'"
    >
        {{ labels[status] ?? status }}
    </span>
</template>
