<script setup lang="ts">
interface Props {
    title: string,
    description: string,
}
defineProps<Props>()
</script>

<template>
    <div class="px-6 py-14 text-center">
        <h2 class="text-base font-semibold text-foreground">
            {{ title }}
        </h2>
        <p class="mt-2 text-sm text-muted">
            {{ description }}
        </p>
        <div v-if="$slots.actions" class="mt-5">
            <slot name="actions" />
        </div>
    </div>
</template>
