<script setup lang="ts">
import SharedCard from './Card.vue'

type Padding = 'none' | 'sm' | 'md' | 'lg'

interface Props {
    padding?: Padding,
}

defineOptions({ inheritAttrs: false })

withDefaults(defineProps<Props>(), {
    padding: 'md',
})
</script>

<template>
    <SharedCard :padding="padding" v-bind="$attrs">
        <template v-if="$slots.header" #header>
            <slot name="header" />
        </template>
        <slot />
        <template v-if="$slots.footer" #footer>
            <slot name="footer" />
        </template>
    </SharedCard>
</template>
