<script setup lang="ts">
interface Props {
    from: number | null,
    to: number | null,
    total: number,
}

const props = defineProps<Props>()
</script>

<template>
    <span>
        Showing <strong>{{ props.from ?? 0 }}</strong>–<strong>{{ props.to ?? 0 }}</strong> of <strong>{{ props.total }}</strong>
    </span>
</template>
