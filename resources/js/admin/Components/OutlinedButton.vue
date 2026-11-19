<script setup lang="ts">
import { computed } from 'vue'
import SharedButton from './Button.vue'

type Variant = 'primary' | 'secondary' | 'danger'
type ButtonType = 'button' | 'submit' | 'reset'

interface Props {
    variant: Variant,
    type?: ButtonType,
}

defineOptions({ inheritAttrs: false })

const props = withDefaults(defineProps<Props>(), {
    type: 'button',
})

const buttonClasses = computed(() => props.variant === 'danger'
    ? 'hover:!bg-surface-muted !border-danger !text-danger'
    : undefined)
</script>

<template>
    <SharedButton
        v-bind="$attrs"
        variant="outline"
        :class="buttonClasses"
        :type="type"
    >
        <slot />
        <template v-if="$slots.leading" #leading>
            <slot name="leading" />
        </template>
        <template v-if="$slots.trailing" #trailing>
            <slot name="trailing" />
        </template>
    </SharedButton>
</template>
