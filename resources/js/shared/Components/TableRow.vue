<script setup lang="ts">
withDefaults(defineProps<{ hoverable?: boolean }>(), {
    hoverable: true,
})
</script>

<template>
    <tr :class="hoverable ? 'transition-colors motion-reduce:transition-none hover:bg-surface-muted' : ''">
        <slot />
    </tr>
</template>
