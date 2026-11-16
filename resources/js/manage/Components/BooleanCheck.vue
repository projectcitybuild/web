<script setup>
import { Icons } from '../Icons';
import SvgIcon from './SvgIcon.vue'

defineProps({
    value: Boolean,
})
</script>

<template>
    <SvgIcon v-if="value" :svg="Icons.check" class="size-4" />
    <SvgIcon v-else :svg="Icons.close" class="size-4 text-subtle" />
</template>
