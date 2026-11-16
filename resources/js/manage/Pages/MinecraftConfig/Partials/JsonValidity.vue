<script setup>
import Spinner from '../../../Components/Spinner.vue'

defineProps({
    isValid: Boolean,
    isParsing: Boolean,
})
</script>

<template>
    <div
        role="status"
        aria-live="polite"
        class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-semibold"
        :class="isParsing
            ? 'border-gray-200 bg-gray-50 text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300'
            : isValid
                ? 'border-green-200 bg-green-50 text-green-700 dark:border-green-800 dark:bg-green-950/50 dark:text-green-300'
                : 'border-red-200 bg-red-50 text-red-700 dark:border-red-800 dark:bg-red-950/50 dark:text-red-300'"
    >
        <Spinner v-if="isParsing" />
        <template v-else>{{ isValid ? 'Valid JSON' : 'Invalid JSON' }}</template>
    </div>
</template>
