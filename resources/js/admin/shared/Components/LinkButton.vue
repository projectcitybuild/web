<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { useAttrs } from 'vue'
import { getButtonClasses, type ButtonSize, type ButtonVariant } from './buttonStyles'

interface Props {
    href: string,
    variant?: ButtonVariant,
    size?: ButtonSize,
}

defineOptions({ inheritAttrs: false })

const props = withDefaults(defineProps<Props>(), {
    variant: 'primary',
    size: 'md',
})

const attrs = useAttrs()
</script>

<template>
    <Link v-bind="attrs" :href="href" :class="getButtonClasses(variant, size)">
        <span v-if="$slots.leading" class="inline-flex shrink-0" aria-hidden="true">
            <slot name="leading" />
        </span>
        <slot />
        <span v-if="$slots.trailing" class="inline-flex shrink-0" aria-hidden="true">
            <slot name="trailing" />
        </span>
    </Link>
</template>
