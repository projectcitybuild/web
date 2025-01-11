<script setup lang="ts">
import { computed } from 'vue'

type Variant = 'default' | 'danger' | 'success'

interface Props {
    variant?: Variant,
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'default',
})

const style = computed(() => {
    switch (props.variant) {
        case 'danger':
            return 'bg-red-100 text-red-800 me-2 dark:bg-red-900 dark:text-red-300'
        case 'success':
            return 'bg-green-100 text-green-800 me-2 dark:bg-green-900 dark:text-green-300'
        case 'default':
        default:
            return 'bg-gray-200'
    }
})
</script>

<template>
    <span :class="'py-1 px-2 rounded-md text-xs whitespace-nowrap ' + style">
        <slot />
    </span>
</template>
