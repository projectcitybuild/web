<script setup lang="ts">
import { computed } from 'vue'

interface Styleable {
    style: string[]
}

class PrimaryVariant implements Styleable {
    readonly style = [
        'text-white',
        'bg-gray-900',
        'hover:bg-gray-800',
        'focus:ring-gray-300',
        'disabled:bg-gray-100', 'disabled:hover:bg-gray-100', 'disabled:text-gray-300',
    ]
}
class SecondaryVariant implements Styleable {
    readonly style = [
        'text-gray-600',
        'bg-gray-200',
        'hover:bg-gray-300',
        'focus:ring-gray-200',
    ]
}
class DangerVariant implements Styleable {
    readonly style = [
        'text-white',
        'bg-red-700',
        'hover:bg-red-600',
        'focus:ring-red-200',
    ]
}

interface Props {
    variant: 'primary' | 'secondary' | 'danger'
    disabled?: boolean
}
const props = defineProps<Props>()

const buttonVariant = computed(() => {
    switch (props.variant) {
        case 'primary': return new PrimaryVariant()
        case 'secondary': return new SecondaryVariant()
        case 'danger': return new DangerVariant()
    }
})

const aggregateStyle = computed(() => {
    const base = [
        'rounded-md',
        'flex', 'items-center', 'justify-center', 'gap-2',
        'focus:ring-4', 'focus:outline-none',
        'px-4', 'py-2',
        'text-sm', 'font-medium',
        'disabled:bg-gray-100', 'disabled:text-gray-300',
    ]
    const aggregate = [
        ...base,
        ...buttonVariant.value.style,
    ]
    return aggregate.join(' ')
})

const disabled = computed(() => props.disabled ?? false)
</script>

<template>
    <button
        type="button"
        :class="aggregateStyle"
        :disabled="disabled"
    >
        <slot />
    </button>
</template>
