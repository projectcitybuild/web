<script setup lang="ts">
import SvgIcon from './SvgIcon.vue'
import { Icons } from '../Components/SvgIcon.vue'

defineProps({
    message: String,
})
</script>

<template>
    <div
        class="flex items-center border-t-4 border-success bg-success-soft p-4 text-success"
        role="alert"
    >
        <SvgIcon :svg="Icons.info" class="size-4 shrink-0" aria-hidden="true" />
        <div class="ms-3 text-sm font-medium">{{ message }}</div>
    </div>
</template>
