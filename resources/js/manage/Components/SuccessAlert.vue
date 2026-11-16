<script setup>
import SvgIcon from './SvgIcon.vue'
import { Icons } from '../Icons'

defineProps({
    message: String,
})
</script>

<template>
    <div
        class="flex items-center p-4 text-green-800 border-t-4 border-green-300 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800"
        role="alert">
        <SvgIcon :svg="Icons.info" class="size-4 shrink-0" aria-hidden="true" />
        <div class="ms-3 text-sm font-medium">{{ message }}</div>
    </div>
</template>
