<script setup lang="ts">
import { computed, useAttrs } from 'vue'

type Variant = 'default' | 'outlined' | 'elevated' | 'muted'
type Padding = 'none' | 'sm' | 'md' | 'lg'

interface Props {
    variant?: Variant,
    padding?: Padding,
}

defineOptions({ inheritAttrs: false })

const props = withDefaults(defineProps<Props>(), {
    variant: 'default',
    padding: 'md',
})

const attrs = useAttrs()
const slots = defineSlots<{
    default?: () => unknown,
    header?: () => unknown,
    footer?: () => unknown,
}>()

const variantClasses: Record<Variant, string> = {
    default: 'bg-transparent',
    outlined: 'bg-transparent',
    elevated: 'bg-transparent',
    muted: 'bg-transparent',
}

const paddingClasses: Record<Padding, string> = {
    none: 'p-0',
    sm: 'p-3',
    md: 'p-4',
    lg: 'p-6',
}

const cardClasses = computed(() => [
    variantClasses[props.variant],
    paddingClasses[props.padding],
])
</script>

<template>
    <div v-bind="attrs" :class="cardClasses">
        <header v-if="slots.header" class="mb-4 border-b border-border pb-3">
            <slot name="header" />
        </header>
        <slot />
        <footer v-if="slots.footer" class="mt-4 border-t border-border pt-3">
            <slot name="footer" />
        </footer>
    </div>
</template>
