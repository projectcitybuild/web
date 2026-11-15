<script setup lang="ts">
import SharedBadge from '../../shared/Components/Badge.vue'

type Variant = 'default' | 'danger' | 'success'

interface Props {
    variant?: Variant,
}

defineOptions({ inheritAttrs: false })

withDefaults(defineProps<Props>(), {
    variant: 'default',
})
</script>

<template>
    <SharedBadge :variant="variant" v-bind="$attrs">
        <slot />
    </SharedBadge>
</template>
