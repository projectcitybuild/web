<script setup>
import { Icons } from '../Icons';
import SvgIcon from './SvgIcon.vue'

defineProps({
    value: Boolean,
})
</script>

<template>
    <SvgIcon v-if="value" :svg="Icons.check" :thickness="3" class="size-4" />
    <SvgIcon v-else :svg="Icons.close" :thickness="3" class="size-4 text-gray-300" />
</template>
