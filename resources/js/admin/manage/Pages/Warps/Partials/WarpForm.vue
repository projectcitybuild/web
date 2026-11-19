<script setup lang="ts">
import { Routes } from '../../../../Routes'
import { InertiaForm, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import CoordinateFields from '../../../Components/Forms/CoordinateFields.vue'
import DangerZone from '../../../Components/Forms/DangerZone.vue'
import FormActions from '../../../Components/Forms/FormActions.vue'
import FormErrorSummary from '../../../Components/Forms/FormErrorSummary.vue'
import FormField from '../../../Components/Forms/FormField.vue'
import FormSection from '../../../Components/Forms/FormSection.vue'
import Input from '../../../../shared/Components/Input.vue'
import { useUnsavedChangesWarning } from '../../../Components/Forms/useUnsavedChangesWarning'

interface Props {
    warp?: App.Http.Data.MinecraftWarpData,
    submit: (form: InertiaForm<WarpFormData>) => void,
}

interface WarpFormData {
    name: string,
    world: string,
    x: number | string,
    y: number | string,
    z: number | string,
    pitch: number | string,
    yaw: number | string,
    created_at: Date,
}

const props = defineProps<Props>()

const form = useForm<WarpFormData>({
    name: props.warp?.name ?? '',
    world: props.warp?.world ?? '',
    x: props.warp?.x ?? 0,
    y: props.warp?.y ?? 0,
    z: props.warp?.z ?? 0,
    pitch: props.warp?.pitch ?? 0,
    yaw: props.warp?.yaw ?? 0,
    created_at: props.warp?.created_at
        ? new Date(props.warp.created_at)
        : new Date(),
})

const isEdit = computed(() => props.warp != null)
useUnsavedChangesWarning(form)

function submit(): void {
    if (!form.processing) {
        props.submit(form)
    }
}

function destroy(): void {
    if (props.warp && !form.processing) {
        form.delete(`/manage/minecraft/warps/${props.warp.id}`)
    }
}

type Axis = 'x' | 'y' | 'z' | 'pitch' | 'yaw'

function updateCoordinates(values: Partial<Record<Axis, number | string>>): void {
    Object.assign(form, values)
}

function updateOrientation(values: Partial<Record<Axis, number | string>>): void {
    Object.assign(form, values)
}
</script>

<template>
    <form class="grid gap-6" @submit.prevent="submit">
        <FormErrorSummary :errors="form.errors" />

        <FormSection
            title="Warp Information"
            description="Set the warp's name and the Minecraft world where it is located."
        >
            <div class="grid gap-4 sm:grid-cols-2">
                <FormField
                    id="name"
                    label="Warp name"
                    description="Case-sensitive. Do not include spaces."
                    :error="form.errors.name ?? undefined"
                    required
                    v-slot="field"
                >
                    <Input v-bind="field" v-model="form.name" />
                </FormField>
                <FormField
                    id="world"
                    label="World name"
                    description="Minecraft world names are case-sensitive."
                    :error="form.errors.world ?? undefined"
                    required
                    v-slot="field"
                >
                    <Input v-bind="field" v-model="form.world" />
                </FormField>
            </div>
        </FormSection>

        <FormSection
            title="Location"
            description="Enter the block coordinates of the warp."
        >
            <CoordinateFields
                group="coordinates"
                :model-value="{ x: form.x, y: form.y, z: form.z }"
                :errors="{ x: form.errors.x ?? undefined, y: form.errors.y ?? undefined, z: form.errors.z ?? undefined }"
                @update:model-value="updateCoordinates"
            />
        </FormSection>

        <FormSection
            title="Orientation"
            description="Set the direction a player will face when arriving at the warp."
        >
            <CoordinateFields
                group="orientation"
                :model-value="{ pitch: form.pitch, yaw: form.yaw }"
                :errors="{ pitch: form.errors.pitch ?? undefined, yaw: form.errors.yaw ?? undefined }"
                @update:model-value="updateOrientation"
            />
        </FormSection>

        <FormActions
            :submit-label="isEdit ? 'Update warp' : 'Create warp'"
            :cancel-href="Routes.manage.warps.list"
            :processing="form.processing"
            sticky
        />

        <DangerZone
            v-if="isEdit"
            title="Delete warp"
            description="Permanently remove this warp from the Minecraft server directory."
            confirm-message="This cannot be undone. Are you sure you want to delete this warp?"
            confirm-title="Yes, delete warp"
            :disabled="form.processing"
            @confirm="destroy"
        />
    </form>
</template>
