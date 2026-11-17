<script setup lang="ts">
import { computed } from 'vue'
import useTheme from '../useTheme'
import SvgIcon from '../../../../manage/Components/SvgIcon.vue'
import { Icons } from '../../../../manage/Icons'

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
        class="inline-flex size-10 items-center justify-center rounded-md text-muted hover:bg-surface-hover hover:text-foreground focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus"
        :aria-label="`Theme: ${preference === 'system' ? 'automatic' : preference}. Switch to ${nextTheme} theme`"
        :title="`Theme: ${preference === 'system' ? 'automatic' : preference}. Switch to ${nextTheme} theme`"
        @click="cycleTheme"
    >
        <SvgIcon v-if="preference === 'light'" :svg="Icons.sun" class="size-5" aria-hidden="true" />
        <SvgIcon v-else-if="preference === 'dark'" :svg="Icons.moon" class="size-5" aria-hidden="true" />
        <SvgIcon v-else :svg="Icons.deviceDesktop" class="size-5" aria-hidden="true" />
    </button>
</template>
