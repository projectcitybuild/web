<script setup lang="ts">
import type { Component } from 'vue'

interface Props {
    svg: Component
}

export type IconComponent = Component

defineProps<Props>()
</script>

<template>
    <component :is="svg" class="size-4" />
</template>
