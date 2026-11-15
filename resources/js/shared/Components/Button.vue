<script setup lang="ts">
import { computed, useAttrs } from 'vue'

type Variant = 'primary' | 'secondary' | 'outline' | 'ghost' | 'danger' | 'link'
type Size = 'sm' | 'md' | 'lg'
type ButtonType = 'button' | 'submit' | 'reset'

interface Props {
    variant?: Variant,
    size?: Size,
    type?: ButtonType,
    disabled?: boolean,
    loading?: boolean,
    iconOnly?: boolean,
}

defineOptions({ inheritAttrs: false })

const props = withDefaults(defineProps<Props>(), {
    variant: 'primary',
    size: 'md',
    type: 'button',
    disabled: false,
    loading: false,
    iconOnly: false,
})

const attrs = useAttrs()

const variantClasses: Record<Variant, string> = {
    primary: 'bg-primary text-primary-foreground hover:bg-primary-hover active:bg-primary-active',
    secondary: 'bg-surface-muted text-foreground hover:bg-surface-hover active:bg-surface-hover',
    outline: 'border border-border-strong bg-surface text-foreground hover:bg-surface-muted active:bg-surface-hover',
    ghost: 'bg-transparent text-foreground hover:bg-surface-muted active:bg-surface-hover',
    danger: 'bg-danger text-danger-foreground hover:bg-danger-hover active:bg-danger-hover',
    link: 'bg-transparent text-primary underline-offset-4 hover:underline active:text-primary-active',
}

const sizeClasses: Record<Size, string> = {
    sm: 'min-h-8 px-3 text-xs',
    md: 'min-h-10 px-4 text-sm',
    lg: 'min-h-12 px-5 text-base',
}

const iconSizeClasses: Record<Size, string> = {
    sm: 'size-8 p-0',
    md: 'size-10 p-0',
    lg: 'size-12 p-0',
}

const isDisabled = computed(() => props.disabled || props.loading)
const buttonClasses = computed(() => [
    'inline-flex items-center justify-center gap-2 rounded-[var(--radius-md)] font-medium transition-colors motion-reduce:transition-none',
    'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus focus-visible:ring-offset-2 focus-visible:ring-offset-surface',
    'disabled:cursor-not-allowed disabled:border-disabled disabled:bg-disabled disabled:text-disabled-foreground disabled:hover:bg-disabled disabled:active:bg-disabled',
    props.iconOnly ? iconSizeClasses[props.size] : sizeClasses[props.size],
    variantClasses[props.variant],
])
</script>

<template>
    <button
        v-bind="attrs"
        :type="type"
        :disabled="isDisabled"
        :aria-busy="loading ? 'true' : undefined"
        :class="buttonClasses"
    >
        <span
            v-if="loading"
            class="size-4 shrink-0 animate-spin rounded-full border-2 border-current border-r-transparent motion-reduce:animate-none"
            aria-hidden="true"
        />
        <span v-else-if="$slots.leading" class="inline-flex shrink-0" aria-hidden="true">
            <slot name="leading" />
        </span>
        <slot />
        <span v-if="$slots.trailing" class="inline-flex shrink-0" aria-hidden="true">
            <slot name="trailing" />
        </span>
    </button>
</template>
