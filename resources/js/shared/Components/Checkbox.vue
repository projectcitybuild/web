<script setup lang="ts">
import { computed, useAttrs, useId } from 'vue'

interface Props {
    modelValue?: boolean,
    id?: string,
    label?: string,
    description?: string,
    error?: string,
    hideLabel?: boolean,
    hideFeedback?: boolean,
}

defineOptions({ inheritAttrs: false })

const props = withDefaults(defineProps<Props>(), {
    modelValue: false,
})
const emit = defineEmits<{
    'update:modelValue': [value: boolean],
}>()

const attrs = useAttrs()
const generatedId = useId()
const checkboxId = computed(() => props.id || generatedId)
const descriptionId = computed(() => `${checkboxId.value}-description`)
const errorId = computed(() => `${checkboxId.value}-error`)

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
    'mt-0.5 size-4 shrink-0 rounded border-border-strong accent-primary focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus disabled:cursor-not-allowed disabled:opacity-60',
    props.error ? 'border-danger accent-danger focus-visible:ring-danger' : '',
])

function updateValue(event: Event): void {
    emit('update:modelValue', (event.target as HTMLInputElement).checked)
}
</script>

<template>
    <div class="grid gap-1.5">
        <div class="flex items-start gap-2">
            <input
                v-bind="attrs"
                :id="checkboxId"
                type="checkbox"
                :checked="modelValue"
                :aria-describedby="describedBy"
                :aria-invalid="error ? 'true' : attrs['aria-invalid']"
                :class="controlClasses"
                @change="updateValue"
            >
            <label v-if="label && !hideLabel" :for="checkboxId" class="text-sm font-medium text-foreground">
                {{ label }}
            </label>
        </div>
        <p v-if="description && !hideFeedback" :id="descriptionId" class="text-sm text-muted" :class="label ? 'ms-6' : ''">
            {{ description }}
        </p>
        <p v-if="error && !hideFeedback" :id="errorId" class="text-sm text-danger" role="alert" :class="label ? 'ms-6' : ''">
            {{ error }}
        </p>
    </div>
</template>
