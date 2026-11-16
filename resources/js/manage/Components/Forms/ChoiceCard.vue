<script setup lang="ts">
interface Props {
    modelValue: string | number,
    value: string | number,
    name: string,
    title: string,
    description?: string,
    disabled?: boolean,
}

const props = withDefaults(defineProps<Props>(), {
    disabled: false,
})

const emit = defineEmits<{
    'update:modelValue': [value: string | number],
}>()
</script>

<template>
    <label
        class="flex min-h-11 cursor-pointer items-start gap-3 rounded-lg border p-4 transition-colors focus-within:ring-2 focus-within:ring-focus"
        :class="[
            modelValue === value ? 'border-primary bg-primary/5' : 'border-border bg-surface hover:bg-surface-hover',
            disabled ? 'cursor-not-allowed opacity-60' : '',
        ]"
    >
        <input
            type="radio"
            :name="name"
            :value="value"
            :checked="modelValue === value"
            :disabled="disabled"
            class="mt-1 size-4 shrink-0 accent-primary focus-visible:ring-2 focus-visible:ring-focus"
            @change="emit('update:modelValue', props.value)"
        >
        <span v-if="$slots.icon" class="mt-0.5 shrink-0 text-muted" aria-hidden="true">
            <slot name="icon" />
        </span>
        <span class="grid gap-1">
            <span class="text-sm font-medium text-foreground">{{ title }}</span>
            <span v-if="description" class="text-sm text-muted">{{ description }}</span>
        </span>
    </label>
</template>
