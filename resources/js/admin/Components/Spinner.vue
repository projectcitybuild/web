<script setup lang="ts">
import SvgIcon from './SvgIcon.vue'
import { Icons } from '../Components/SvgIcon.vue'
</script>

<template>
    <div role="status">
        <SvgIcon :svg="Icons.loader" class="size-4 animate-spin text-primary" aria-hidden="true" />
        <span class="sr-only">Loading...</span>
    </div>
</template>
