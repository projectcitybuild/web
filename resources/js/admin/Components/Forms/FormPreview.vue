<script setup lang="ts">
interface Props {
    title?: string,
    description?: string,
    live?: boolean,
    empty?: boolean,
    emptyMessage?: string,
}

withDefaults(defineProps<Props>(), {
    live: false,
    empty: false,
    emptyMessage: 'A preview will appear here as you complete the form.',
})
</script>

<template>
    <section class="grid content-start gap-4 rounded-lg border border-border bg-surface p-5 sm:p-6">
        <header v-if="title || description || live" class="grid gap-1">
            <div class="flex items-center justify-between gap-3">
                <h2 v-if="title" class="text-base font-semibold text-foreground">{{ title }}</h2>
                <span v-if="live" class="inline-flex items-center gap-1.5 text-xs text-success">
                    <span class="size-2 rounded-full bg-success" aria-hidden="true" />
                    Live preview
                </span>
            </div>
            <p v-if="description" class="text-sm text-muted">{{ description }}</p>
        </header>
        <div v-if="empty" class="rounded-md border border-dashed border-border-strong bg-surface-muted p-5 text-sm text-muted">
            <slot name="empty">{{ emptyMessage }}</slot>
        </div>
        <slot v-else />
    </section>
</template>
