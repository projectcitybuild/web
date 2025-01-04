<script setup lang="ts">
import { computed } from 'vue'

interface Styleable {
    style: string[]
}

class PrimaryVariant implements Styleable {
    readonly style = [
        'text-white',
        'border-gray-900',
        'bg-white',
        'hover:bg-gray-200',
        'focus:ring-gray-300',
        'disabled:bg-gray-600', 'disabled:hover:bg-gray-600', 'disabled:text-gray-400',
    ]
}
class SecondaryVariant implements Styleable {
    readonly style = [
        'text-gray-500',
        'border-gray-300',
        'bg-white',
        'hover:bg-gray-50',
        'focus:ring-gray-100',
        'disabled:bg-gray-600', 'disabled:hover:bg-gray-600', 'disabled:text-gray-400',
    ]
}
class DangerVariant implements Styleable {
    readonly style = [
        'text-red-500',
        'border-red-500',
        'bg-white',
        'hover:bg-red-50',
        'focus:ring-red-100',
        'disabled:bg-gray-600', 'disabled:hover:bg-gray-600', 'disabled:text-gray-400',
    ]
}

interface Props {
    variant: 'primary' | 'secondary' | 'danger',
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
        'border',
        'focus:ring-4', 'focus:outline-none',
        'px-4', 'py-2',
        'text-sm', 'font-medium',
    ]
    const aggregate = [
        ...base,
        ...buttonVariant.value.style,
    ]
    return aggregate.join(' ')
})
</script>

<template>
    <button
        type="button"
        :class="aggregateStyle"
    >
        <slot />
    </button>
</template>
