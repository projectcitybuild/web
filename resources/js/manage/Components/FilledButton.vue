<script setup lang="ts">
import SharedButton from '../../shared/Components/Button.vue'

type Variant = 'primary' | 'secondary' | 'danger'
type Size = 'sm' | 'md' | 'lg'
type ButtonType = 'button' | 'submit' | 'reset'

interface Props {
    variant: Variant,
    size?: Size,
    disabled?: boolean,
    loading?: boolean,
    type?: ButtonType,
}

defineOptions({ inheritAttrs: false })

withDefaults(defineProps<Props>(), {
    disabled: false,
    loading: false,
    size: 'md',
    type: 'button',
})
</script>

<template>
    <SharedButton
        v-bind="$attrs"
        :variant="variant"
        :size="size"
        :disabled="disabled"
        :loading="loading"
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
