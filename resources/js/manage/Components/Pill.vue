<script setup lang="ts">
import { computed } from 'vue'

type Variant = 'default' | 'danger'

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
        case 'default':
        default:
            return 'bg-gray-200'
    }
})
</script>

<template>
    <span :class="'py-1 px-2 text-xs rounded-md ' + style">
        <slot/>
    </span>
</template>
