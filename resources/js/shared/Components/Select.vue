<script setup lang="ts">
import { computed, useAttrs, useId } from 'vue'

interface Props {
    modelValue?: string | number | null,
    modelModifiers?: { number?: boolean },
    id?: string,
    label?: string,
    description?: string,
    error?: string,
    placeholder?: string,
}

defineOptions({ inheritAttrs: false })

const props = defineProps<Props>()
const emit = defineEmits<{
    'update:modelValue': [value: string | number],
}>()

const attrs = useAttrs()
const slots = defineSlots<{ default?: () => unknown }>()
const generatedId = useId()
const selectId = computed(() => props.id || generatedId)
const descriptionId = computed(() => `${selectId.value}-description`)
const errorId = computed(() => `${selectId.value}-error`)

const describedBy = computed(() => {
    const externalId = attrs['aria-describedby']
    const ids = typeof externalId === 'string' ? [externalId] : []

    if (props.description) {
        ids.push(descriptionId.value)
    }

    if (props.error) {
        ids.push(errorId.value)
    }

    return ids.join(' ') || undefined
})

const controlClasses = computed(() => [
    'block min-h-10 w-full rounded-[var(--radius-md)] border border-border-strong bg-surface px-3 py-2 text-sm text-foreground',
    'focus-visible:border-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus',
    'disabled:cursor-not-allowed disabled:border-disabled disabled:bg-disabled disabled:text-disabled-foreground',
    props.error ? 'border-danger focus-visible:border-danger focus-visible:ring-danger' : '',
])

function updateValue(event: Event): void {
    const value = (event.target as HTMLSelectElement).value

    if (props.modelModifiers?.number && value !== '') {
        const number = Number(value)
        emit('update:modelValue', Number.isNaN(number) ? value : number)
        return
    }

    emit('update:modelValue', value)
}
</script>

<template>
    <div class="grid gap-1.5">
        <label v-if="label" :for="selectId" class="text-sm font-medium text-foreground">
            {{ label }}
        </label>
        <select
            v-bind="attrs"
            :id="selectId"
            :value="modelValue ?? ''"
            :aria-describedby="describedBy"
            :aria-invalid="error ? 'true' : attrs['aria-invalid']"
            :class="controlClasses"
            @change="updateValue"
        >
            <option v-if="placeholder" value="" disabled>{{ placeholder }}</option>
            <slot v-if="slots.default" />
        </select>
        <p v-if="description" :id="descriptionId" class="text-sm text-muted">
            {{ description }}
        </p>
        <p v-if="error" :id="errorId" class="text-sm text-danger" role="alert">
            {{ error }}
        </p>
    </div>
</template>
