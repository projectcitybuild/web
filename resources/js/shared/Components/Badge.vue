<script setup lang="ts">
import { computed, useAttrs } from 'vue'

type Variant = 'default' | 'primary' | 'success' | 'warning' | 'danger' | 'info'
type Size = 'sm' | 'md'
type Tone = 'subtle' | 'solid'

interface Props {
    variant?: Variant,
    size?: Size,
    tone?: Tone,
}

defineOptions({ inheritAttrs: false })

const props = withDefaults(defineProps<Props>(), {
    variant: 'default',
    size: 'md',
    tone: 'subtle',
})

const attrs = useAttrs()
const slots = defineSlots<{
    default?: () => unknown,
    icon?: () => unknown,
}>()

const variantClasses: Record<Variant, string> = {
    default: 'border-border bg-surface-muted text-muted',
    primary: 'border-primary bg-surface text-primary',
    success: 'border-success bg-surface text-success',
    warning: 'border-warning bg-surface text-warning',
    danger: 'border-danger bg-surface text-danger',
    info: 'border-info bg-surface text-info',
}

const solidVariantClasses: Record<Exclude<Variant, 'default'>, string> = {
    primary: 'border-transparent bg-primary text-primary-foreground',
    success: 'border-transparent bg-success text-success-foreground',
    warning: 'border-transparent bg-warning text-warning-foreground',
    danger: 'border-transparent bg-danger text-danger-foreground',
    info: 'border-transparent bg-info text-info-foreground',
}

const sizeClasses: Record<Size, string> = {
    sm: 'min-h-5 px-1.5 py-0.5 text-xs',
    md: 'min-h-6 px-2 py-0.5 text-xs',
}

const badgeClasses = computed(() => [
    'inline-flex items-center gap-1 rounded-[var(--radius-full)] border font-medium [&>svg]:size-3',
    props.tone === 'solid' && props.variant !== 'default'
        ? solidVariantClasses[props.variant]
        : variantClasses[props.variant],
    sizeClasses[props.size],
])
</script>

<template>
    <span v-bind="attrs" :class="badgeClasses">
        <span v-if="slots.icon" class="inline-flex shrink-0" aria-hidden="true">
            <slot name="icon" />
        </span>
        <slot />
    </span>
</template>
