<script setup lang="ts">
import { computed } from 'vue'
import DateTimePicker from '../DateTimePicker.vue'
import FormField from './FormField.vue'

interface Props {
    id: string,
    label: string,
    description?: string,
    error?: string,
    required?: boolean,
    disabled?: boolean,
}

const props = withDefaults(defineProps<Props>(), {
    required: false,
    disabled: false,
})

const model = defineModel<Date | null>()

const describedBy = computed(() => [
    props.description ? `${props.id}-description` : null,
    props.error ? `${props.id}-error` : null,
].filter(Boolean).join(' ') || undefined)
</script>

<template>
    <FormField
        :id="id"
        :label="label"
        :description="description"
        :error="error"
        :required="required"
    >
        <DateTimePicker
            v-model="model"
            :aria-label="label"
            :aria-describedby="describedBy"
            :aria-invalid="error ? 'true' : undefined"
            :disabled="disabled"
        />
    </FormField>
</template>
