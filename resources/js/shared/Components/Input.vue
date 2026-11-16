<script setup lang="ts">
import { computed, useAttrs, useId } from 'vue'

interface Props {
    modelValue?: string | number | null,
    modelModifiers?: { number?: boolean, trim?: boolean },
    id?: string,
    type?: string,
    label?: string,
    description?: string,
    error?: string,
    hideLabel?: boolean,
    hideFeedback?: boolean,
}

defineOptions({ inheritAttrs: false })

const props = defineProps<Props>()
const emit = defineEmits<{
    'update:modelValue': [value: string | number],
}>()

const attrs = useAttrs()
const generatedId = useId()
const inputId = computed(() => props.id || generatedId)
const descriptionId = computed(() => `${inputId.value}-description`)
const errorId = computed(() => `${inputId.value}-error`)

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
    'block min-h-10 w-full rounded-[var(--radius-md)] border border-border-strong bg-surface px-3 py-2 text-sm text-foreground placeholder:text-subtle',
    'focus-visible:border-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus',
    'disabled:cursor-not-allowed disabled:border-disabled disabled:bg-disabled disabled:text-disabled-foreground read-only:bg-surface-muted',
    props.error ? 'border-danger focus-visible:border-danger focus-visible:ring-danger' : '',
])

function updateValue(event: Event): void {
    let value: string | number = (event.target as HTMLInputElement).value

    if (props.modelModifiers?.trim && typeof value === 'string') {
        value = value.trim()
    }

    if (props.modelModifiers?.number || props.type === 'number') {
        const number = Number(value)
        value = value !== '' && !Number.isNaN(number) ? number : value
    }

    emit('update:modelValue', value)
}
</script>

<template>
    <div class="grid gap-1.5">
        <label v-if="label && !hideLabel" :for="inputId" class="text-sm font-medium text-foreground">
            {{ label }}
        </label>
        <input
            v-bind="attrs"
            :id="inputId"
            :type="type ?? 'text'"
            :value="modelValue ?? ''"
            :aria-describedby="describedBy"
            :aria-invalid="error ? 'true' : attrs['aria-invalid']"
            :class="controlClasses"
            @input="updateValue"
        >
        <p v-if="description && !hideFeedback" :id="descriptionId" class="text-sm text-muted">
            {{ description }}
        </p>
        <p v-if="error && !hideFeedback" :id="errorId" class="text-sm text-danger" role="alert">
            {{ error }}
        </p>
    </div>
</template>
