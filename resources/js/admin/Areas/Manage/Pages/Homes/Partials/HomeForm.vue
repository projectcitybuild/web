<script setup lang="ts">
import { Routes } from '../../../../../Navigation/Routes'
import { InertiaForm, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import PlayerPicker from '../../../../../Components/PlayerPicker.vue'
import CoordinateFields from '../../../../../Components/Forms/CoordinateFields.vue'
import DangerZone from '../../../../../Components/Forms/DangerZone.vue'
import FormActions from '../../../../../Components/Forms/FormActions.vue'
import FormErrorSummary from '../../../../../Components/Forms/FormErrorSummary.vue'
import FormField from '../../../../../Components/Forms/FormField.vue'
import FormSection from '../../../../../Components/Forms/FormSection.vue'
import { useUnsavedChangesWarning } from '../../../../../Components/Forms/useUnsavedChangesWarning'
import Input from '../../../../../Components/Input.vue'

interface Props {
    home?: App.Http.Data.MinecraftHomeData,
    submit: (form: InertiaForm<HomeFormData>) => void,
}

interface HomeFormData {
    player_uuid: string | null,
    player_alias: string | null,
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

const form = useForm<HomeFormData>({
    player_uuid: props.home?.player?.uuid ?? null,
    player_alias: props.home?.player?.alias ?? null,
    name: props.home?.name ?? '',
    world: props.home?.world ?? '',
    x: props.home?.x ?? 0,
    y: props.home?.y ?? 0,
    z: props.home?.z ?? 0,
    pitch: props.home?.pitch ?? 0,
    yaw: props.home?.yaw ?? 0,
    created_at: props.home?.created_at
        ? new Date(props.home.created_at)
        : new Date(),
})

const isEdit = computed(() => props.home != null)
useUnsavedChangesWarning(form)

function submit(): void {
    props.submit(form)
}

function destroy(): void {
    if (props.home) {
        form.delete(`/manage/homes/${props.home.id}`)
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
            title="Home Ownership"
            description="Select the player who owns this home."
        >
            <FormField
                id="player_id"
                label="Player"
                :error="form.errors.player_uuid ?? undefined"
                required
            >
                <PlayerPicker
                    id="player_id"
                    v-model:uuid="form.player_uuid"
                    v-model:alias="form.player_alias"
                />
            </FormField>
        </FormSection>

        <FormSection
            title="Home Information"
            description="Set the home name and the Minecraft world where it is located."
        >
            <div class="grid gap-4 sm:grid-cols-2">
                <FormField
                    id="name"
                    label="Name"
                    description="Case-sensitive and cannot contain white space."
                    :error="form.errors.name ?? undefined"
                    required
                    v-slot="field"
                >
                    <Input v-bind="field" v-model="form.name" />
                </FormField>
                <FormField
                    id="world"
                    label="World Name"
                    description="Warning: Case-sensitive."
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
            description="Enter the block coordinates of the home."
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
            description="Set the direction a player will face when arriving at the home."
        >
            <CoordinateFields
                group="orientation"
                :model-value="{ pitch: form.pitch, yaw: form.yaw }"
                :errors="{ pitch: form.errors.pitch ?? undefined, yaw: form.errors.yaw ?? undefined }"
                @update:model-value="updateOrientation"
            />
        </FormSection>

        <FormActions
            :submit-label="isEdit ? 'Update home' : 'Create home'"
            :cancel-href="Routes.manage.homes.list"
            :processing="form.processing"
            sticky
        />

        <DangerZone
            v-if="isEdit"
            title="Delete home"
            description="Permanently remove this home."
            confirm-message="This cannot be undone."
            confirm-title="Yes, Delete It"
            :disabled="form.processing"
            @confirm="destroy"
        />
    </form>
</template>
