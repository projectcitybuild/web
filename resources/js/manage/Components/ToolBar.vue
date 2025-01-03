<script setup lang="ts">
import Card from './Card.vue'
</script>

<template>
    <Card>
        <div class="flex items-center justify-between gap-2 p-4">
            <slot name="left" />

            <div class="flex gap-2 items-center">
                <slot name="right" />
            </div>
        </div>
    </Card>
</template>
