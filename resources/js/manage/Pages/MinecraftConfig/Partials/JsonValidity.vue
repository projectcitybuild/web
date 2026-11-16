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
            ? 'border-border bg-surface-muted text-muted border-border  dark:text-subtle'
            : isValid
                ? 'border-success bg-success-soft text-success border-success bg-success-soft dark:text-success'
                : 'border-danger bg-danger-soft text-danger border-danger bg-danger-soft dark:text-danger'"
    >
        <Spinner v-if="isParsing" />
        <template v-else>{{ isValid ? 'Valid JSON' : 'Invalid JSON' }}</template>
    </div>
</template>
