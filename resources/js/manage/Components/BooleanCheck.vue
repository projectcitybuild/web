<script setup>
import SvgIcon from './SvgIcon.vue'

defineProps({
    value: Boolean,
})
</script>

<template>
    <SvgIcon v-if="value" icon="check" :thickness="3" class="size-4" />
    <SvgIcon v-else icon="close" :thickness="3" class="size-4 text-gray-300" />
</template>
