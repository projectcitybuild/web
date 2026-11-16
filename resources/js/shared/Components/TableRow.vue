<script setup lang="ts">
withDefaults(defineProps<{ hoverable?: boolean }>(), {
    hoverable: true,
})
</script>

<template>
    <tr :class="hoverable ? 'border-b border-border-table transition-colors last:border-b-0 motion-reduce:transition-none hover:bg-surface-muted' : 'border-b border-border-table last:border-b-0'">
        <slot />
    </tr>
</template>
