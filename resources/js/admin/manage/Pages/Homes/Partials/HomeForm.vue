<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import type { Home } from '../../../Data/Home'
import PlayerPicker from '../../../Components/PlayerPicker.vue'
import CoordinateFields from '../../../Components/Forms/CoordinateFields.vue'
import DangerZone from '../../../Components/Forms/DangerZone.vue'
import FormActions from '../../../Components/Forms/FormActions.vue'
import FormErrorSummary from '../../../Components/Forms/FormErrorSummary.vue'
import FormField from '../../../Components/Forms/FormField.vue'
import FormSection from '../../../Components/Forms/FormSection.vue'
import { useUnsavedChangesWarning } from '../../../Components/Forms/useUnsavedChangesWarning'
import Input from '../../../../shared/Components/Input.vue'

interface Props {
    home?: Home,
    submit: (form: ReturnType<typeof useForm<Home>>) => void,
}

const props = defineProps<Props>()

const form = useForm<Home>({
    player_uuid: props.home?.player?.uuid,
    player_alias: props.home?.player?.alias,
    name: props.home?.name,
    world: props.home?.world,
    x: props.home?.x,
    y: props.home?.y,
    z: props.home?.z,
    pitch: props.home?.pitch,
    yaw: props.home?.yaw,
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

function updateCoordinates(values: Partial<Pick<Home, 'x' | 'y' | 'z'>>): void {
    Object.assign(form, values)
}

function updateOrientation(values: Partial<Pick<Home, 'pitch' | 'yaw'>>): void {
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
                :error="form.errors.player_id"
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
                    :error="form.errors.name"
                    required
                    v-slot="field"
                >
                    <Input v-bind="field" v-model="form.name" />
                </FormField>
                <FormField
                    id="world"
                    label="World Name"
                    description="Warning: Case-sensitive."
                    :error="form.errors.world"
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
                :errors="{ x: form.errors.x, y: form.errors.y, z: form.errors.z }"
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
                :errors="{ pitch: form.errors.pitch, yaw: form.errors.yaw }"
                @update:model-value="updateOrientation"
            />
        </FormSection>

        <FormActions
            :submit-label="isEdit ? 'Update home' : 'Create home'"
            cancel-href="/manage/homes"
            :processing="form.processing"
            sticky
        />

        <DangerZone
            v-if="isEdit"
            title="Delete home"
            description="Permanently remove this home from the directory."
            confirm-message="This cannot be undone."
            confirm-title="Yes, Delete It"
            :disabled="form.processing"
            @confirm="destroy"
        />
    </form>
</template>
