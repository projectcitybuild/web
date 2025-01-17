<script setup lang="ts">
interface Props {
    count: number,
}
defineProps<Props>()
</script>

<template>
    <span class="rounded-md bg-red-600 py-1 px-1 md:px-2 text-white text-xs">
        {{ count }}
    </span>
</template>
