<script setup lang="ts">
import Input from '../../../shared/Components/Input.vue'
import FormPreview from './FormPreview.vue'
import MiniMessageRenderer from '../MiniMessageRenderer.vue'

interface Props {
    modelValue?: string,
    id: string,
    label: string,
    description?: string,
    error?: string,
    placeholder?: string,
    previewPlaceholder?: string,
}

withDefaults(defineProps<Props>(), {
    modelValue: '',
    previewPlaceholder: 'Preview',
})

const emit = defineEmits<{
    'update:modelValue': [value: string],
}>()

function updateValue(value: string | number): void {
    emit('update:modelValue', String(value))
}
</script>

<template>
    <div class="grid gap-3">
        <Input
            :id="id"
            :model-value="modelValue"
            :label="label"
            :description="description"
            :error="error"
            :placeholder="placeholder"
            @update:model-value="updateValue"
        />
        <FormPreview title="Preview" :live="true">
            <MiniMessageRenderer
                :text="modelValue"
                :placeholder="previewPlaceholder"
                class="min-h-6 break-words text-foreground"
            />
        </FormPreview>
    </div>
</template>
