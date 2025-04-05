<script setup lang="ts">
import Card from './Card.vue'
</script>

<template>
    <Card>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 p-4">
            <slot name="left" />

            <div class="flex gap-2 items-center">
                <slot name="right" />
            </div>
        </div>
    </Card>
</template>
