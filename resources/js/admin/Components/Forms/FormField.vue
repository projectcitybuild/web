<script setup lang="ts">
import { computed } from 'vue'

interface Props {
    id: string,
    label: string,
    description?: string,
    error?: string,
    required?: boolean,
}

const props = defineProps<Props>()

const descriptionId = computed(() => `${props.id}-description`)
const errorId = computed(() => `${props.id}-error`)
</script>

<template>
    <div class="grid gap-1.5">
        <label :for="id" class="text-sm font-medium text-foreground">
            {{ label }}<span v-if="required" class="ms-1 text-danger" aria-hidden="true">*</span>
            <span v-if="required" class="sr-only">(required)</span>
        </label>
        <slot
            :id="id"
            :description="description"
            :error="error"
            :hide-label="true"
            :hide-feedback="true"
            :required="required"
            :aria-invalid="error ? 'true' : undefined"
        />
        <p v-if="description" :id="descriptionId" class="text-sm text-muted">
            {{ description }}
        </p>
        <p v-if="error" :id="errorId" class="text-sm text-danger" role="alert">
            {{ error }}
        </p>
    </div>
</template>
