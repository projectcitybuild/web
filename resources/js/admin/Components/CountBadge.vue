<script setup lang="ts">
import SharedBadge from './Badge.vue'

defineOptions({ inheritAttrs: false })

defineProps<{ count: number }>()
</script>

<template>
    <SharedBadge variant="danger" tone="solid" size="sm" v-bind="$attrs">
        <slot>{{ count }}</slot>
    </SharedBadge>
</template>
