<script setup lang="ts">
interface Props {
    title?: string,
    description?: string,
    framed?: boolean,
    divider?: boolean,
}

withDefaults(defineProps<Props>(), {
    framed: false,
    divider: false,
})
</script>

<template>
    <section
        class="grid gap-5"
        :class="[
            framed ? 'rounded-lg border border-border bg-surface p-5 sm:p-6' : '',
            divider ? 'border-b border-border pb-6' : '',
        ]"
    >
        <header v-if="title || description || $slots.icon" class="flex items-start gap-3">
            <span v-if="$slots.icon" class="mt-0.5 shrink-0 text-muted" aria-hidden="true">
                <slot name="icon" />
            </span>
            <div class="grid gap-1">
                <h2 v-if="title" class="text-base font-semibold text-foreground">
                    {{ title }}
                </h2>
                <p v-if="description" class="text-sm text-muted">
                    {{ description }}
                </p>
            </div>
        </header>
        <div class="grid gap-4 sm:gap-5">
            <slot />
        </div>
    </section>
</template>
