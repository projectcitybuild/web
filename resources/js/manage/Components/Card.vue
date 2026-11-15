<script setup lang="ts">
import SharedCard from '../../shared/Components/Card.vue'

defineOptions({ inheritAttrs: false })
</script>

<template>
    <SharedCard padding="none" v-bind="$attrs">
        <template v-if="$slots.header" #header>
            <slot name="header" />
        </template>
        <slot />
        <template v-if="$slots.footer" #footer>
            <slot name="footer" />
        </template>
    </SharedCard>
</template>
