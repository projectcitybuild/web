<script setup lang="ts">
import { computed } from 'vue'
import useTheme from '../useTheme'

const { preference, cycleTheme } = useTheme()
const nextTheme = computed(() => {
    if (preference.value === 'system') {
        return 'light'
    }
    if (preference.value === 'dark') {
        return 'dark';
    }
    return 'automatic'
})
</script>

<template>
    <button
        type="button"
        class="inline-flex size-10 items-center justify-center rounded-md text-muted hover:bg-surface-hover hover:text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus"
        :aria-label="`Theme: ${preference === 'system' ? 'automatic' : preference}. Switch to ${nextTheme} theme`"
        :title="`Theme: ${preference === 'system' ? 'automatic' : preference}. Switch to ${nextTheme} theme`"
        @click="cycleTheme"
    >
        <svg v-if="preference === 'light'" aria-hidden="true" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <circle cx="12" cy="12" r="4" />
            <path stroke-linecap="round" d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42" />
        </svg>
        <svg v-else-if="preference === 'dark'" aria-hidden="true" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.5 15.5A8.5 8.5 0 0 1 8.5 3.5 8.5 8.5 0 1 0 20.5 15.5Z" />
        </svg>
        <svg v-else aria-hidden="true" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="m5 20 7-16 7 16M8 14h8" />
        </svg>
    </button>
</template>
