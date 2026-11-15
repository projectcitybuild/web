<script setup lang="ts">
import { computed, useAttrs, useId } from 'vue'

interface Props {
    modelValue?: string | null,
    id?: string,
    label?: string,
    description?: string,
    error?: string,
}

defineOptions({ inheritAttrs: false })

const props = defineProps<Props>()
const emit = defineEmits<{
    'update:modelValue': [value: string],
}>()

const attrs = useAttrs()
const generatedId = useId()
const textareaId = computed(() => props.id || generatedId)
const descriptionId = computed(() => `${textareaId.value}-description`)
const errorId = computed(() => `${textareaId.value}-error`)

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
    'block min-h-24 w-full rounded-[var(--radius-md)] border border-border-strong bg-surface px-3 py-2 text-sm text-foreground placeholder:text-subtle',
    'focus-visible:border-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus',
    'disabled:cursor-not-allowed disabled:border-disabled disabled:bg-disabled disabled:text-disabled-foreground read-only:bg-surface-muted',
    props.error ? 'border-danger focus-visible:border-danger focus-visible:ring-danger' : '',
])

function updateValue(event: Event): void {
    emit('update:modelValue', (event.target as HTMLTextAreaElement).value)
}
</script>

<template>
    <div class="grid gap-1.5">
        <label v-if="label" :for="textareaId" class="text-sm font-medium text-foreground">
            {{ label }}
        </label>
        <textarea
            v-bind="attrs"
            :id="textareaId"
            :value="modelValue ?? ''"
            :aria-describedby="describedBy"
            :aria-invalid="error ? 'true' : attrs['aria-invalid']"
            :class="controlClasses"
            @input="updateValue"
        />
        <p v-if="description" :id="descriptionId" class="text-sm text-muted">
            {{ description }}
        </p>
        <p v-if="error" :id="errorId" class="text-sm text-danger" role="alert">
            {{ error }}
        </p>
    </div>
</template>
