<script setup lang="ts">
import { ref } from 'vue'

defineProps<{ open: boolean }>()
const emit = defineEmits<{ close: [] }>()

const sidebar = ref<HTMLElement | null>(null)

function focusFirst(): void {
    sidebar.value?.querySelector<HTMLElement>('button, a[href]')?.focus()
}

function closeAfterNavigation(event: MouseEvent): void {
    if (event.target instanceof Element && event.target.closest('a[href]')) {
        emit('close')
    }
}

defineExpose({ focusFirst })
</script>

<template>
    <aside
        id="admin-sidebar"
        ref="sidebar"
        class="fixed bottom-0 left-0 top-16 z-50 w-64 border-r border-border bg-surface transition-transform duration-200 ease-out motion-reduce:transition-none md:visible md:z-30 md:translate-x-0"
        :class="open ? 'visible translate-x-0' : 'invisible -translate-x-full md:visible'"
        :role="open ? 'dialog' : undefined"
        :aria-modal="open ? 'true' : undefined"
        aria-label="Administrative navigation"
        @click="closeAfterNavigation"
    >
        <button
            type="button"
            class="absolute right-3 top-3 inline-flex size-10 items-center justify-center rounded-md text-muted hover:bg-surface-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus md:hidden"
            aria-label="Close navigation menu"
            @click.stop="$emit('close')"
        >
            <svg aria-hidden="true" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" d="m6 6 12 12M18 6 6 18" />
            </svg>
        </button>
        <div class="h-full overflow-y-auto px-3 py-16 md:py-5">
            <nav aria-label="Main navigation">
                <slot />
            </nav>
        </div>
    </aside>
</template>
