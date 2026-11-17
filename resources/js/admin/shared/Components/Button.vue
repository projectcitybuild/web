<script setup lang="ts">
import { computed, useAttrs } from 'vue'
import { getButtonClasses, type ButtonSize, type ButtonVariant } from './buttonStyles'

type ButtonType = 'button' | 'submit' | 'reset'

interface Props {
    variant?: ButtonVariant,
    size?: ButtonSize,
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

const isDisabled = computed(() => props.disabled || props.loading)
const buttonClasses = computed(() => getButtonClasses(props.variant, props.size, props.iconOnly))
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
