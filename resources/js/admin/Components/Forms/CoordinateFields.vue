<script setup lang="ts">
import FormField from './FormField.vue'
import Input from '../Input.vue'

type Axis = 'x' | 'y' | 'z' | 'pitch' | 'yaw'
type Values = Partial<Record<Axis, number | string>>

interface Props {
    modelValue: Values,
    errors?: Partial<Record<Axis, string>>,
    group: 'coordinates' | 'orientation',
}

const props = withDefaults(defineProps<Props>(), {
    errors: () => ({}),
})

const emit = defineEmits<{
    'update:modelValue': [value: Values],
}>()

const axes: Record<Props['group'], Axis[]> = {
    coordinates: ['x', 'y', 'z'],
    orientation: ['pitch', 'yaw'],
}

const descriptions: Record<Axis, string> = {
    x: 'East/west position in blocks.',
    y: 'Vertical position in blocks.',
    z: 'North/south position in blocks.',
    pitch: 'Up/down looking angle in degrees.',
    yaw: 'Horizontal facing angle in degrees.',
}

function update(axis: Axis, value: string | number): void {
    emit('update:modelValue', { ...props.modelValue, [axis]: value })
}
</script>

<template>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <FormField
            v-for="axis in axes[group]"
            :id="axis"
            :key="axis"
            :label="axis.toUpperCase()"
            :description="descriptions[axis]"
            :error="errors[axis]"
            required
            v-slot="field"
        >
            <Input
                v-bind="field"
                :model-value="modelValue[axis]"
                type="number"
                :step="group === 'coordinates' ? 'any' : 1"
                @update:model-value="update(axis, $event)"
            />
        </FormField>
    </div>
</template>
