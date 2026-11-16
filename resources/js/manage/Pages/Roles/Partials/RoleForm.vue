<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import type { Role } from '../../../Data/Role'
import FormActions from '../../../Components/Forms/FormActions.vue'
import FormErrorSummary from '../../../Components/Forms/FormErrorSummary.vue'
import FormField from '../../../Components/Forms/FormField.vue'
import FormSection from '../../../Components/Forms/FormSection.vue'
import DangerZone from '../../../Components/Forms/DangerZone.vue'
import MiniMessageInput from '../../../Components/Forms/MiniMessageInput.vue'
import { useUnsavedChangesWarning } from '../../../Components/Forms/useUnsavedChangesWarning'
import Input from '../../../../shared/Components/Input.vue'
import Select from '../../../../shared/Components/Select.vue'

interface Props {
    role?: Role,
    submit: (form: ReturnType<typeof useForm<Role>>) => void,
}

const props = defineProps<Props>()

const form = useForm<Role>({
    name: props.role?.name,
    alias: props.role?.alias,
    minecraft_name: props.role?.minecraft_name,
    minecraft_display_name: props.role?.minecraft_display_name,
    minecraft_hover_text: props.role?.minecraft_hover_text,
    is_admin: props.role?.is_admin,
    is_default: props.role?.is_default,
    role_type: props.role?.role_type ?? 'build',
    display_priority: props.role?.display_priority,
    additional_homes: props.role?.additional_homes ?? 0,
    created_at: props.role?.created_at
        ? new Date(props.role.created_at)
        : new Date(),
})

const isEdit = computed(() => props.role != null)
useUnsavedChangesWarning(form)

function submit(): void {
    if (!form.processing) {
        props.submit(form)
    }
}

function destroy(): void {
    if (props.role && !form.processing) {
        form.delete(`/manage/roles/${props.role.id}`)
    }
}
</script>

<template>
    <form class="grid gap-6" @submit.prevent="submit">
        <FormErrorSummary :errors="form.errors" />

        <FormSection
            title="General Information"
            description="Set the role's internal identity and type."
        >
            <div class="grid gap-4 sm:grid-cols-2">
                <FormField
                    id="name"
                    label="Name"
                    description="Internal role name used to identify it in Manage."
                    :error="form.errors.name"
                    required
                    v-slot="field"
                >
                    <Input v-bind="field" v-model="form.name" />
                </FormField>
                <FormField
                    id="alias"
                    label="Alias"
                    description="Optional short name shown in places with limited space."
                    :error="form.errors.alias"
                    v-slot="field"
                >
                    <Input v-bind="field" v-model="form.alias" />
                </FormField>
                <FormField
                    id="role_type"
                    label="Role type"
                    :error="form.errors.role_type"
                    required
                    v-slot="field"
                >
                    <Select v-bind="field" v-model="form.role_type">
                        <option value="build">Build</option>
                        <option value="trust">Trust</option>
                        <option value="donor">Donor</option>
                        <option value="staff">Staff</option>
                    </Select>
                </FormField>
            </div>
        </FormSection>

        <FormSection
            title="Minecraft Integration"
            description="Configure how this role is represented on the Minecraft server."
        >
            <FormField
                id="minecraft_name"
                label="Minecraft group name"
                description="The permission-system group name this role is linked to (for example, in LuckPerms)."
                :error="form.errors.minecraft_name"
                v-slot="field"
            >
                <Input v-bind="field" v-model="form.minecraft_name" />
            </FormField>
            <MiniMessageInput
                id="minecraft_display_name"
                v-model="form.minecraft_display_name"
                label="Minecraft display name"
                description="The text shown for this role in chat. Supports MiniMessage formatting."
                :error="form.errors.minecraft_display_name"
                placeholder="<blue>[A]</blue>"
                preview-placeholder="[Role]"
            />
            <MiniMessageInput
                id="minecraft_hover_text"
                v-model="form.minecraft_hover_text"
                label="Minecraft hover text"
                description="The text shown when a player hovers over the display name. Supports MiniMessage formatting."
                :error="form.errors.minecraft_hover_text"
                placeholder="Architect"
            />
        </FormSection>

        <FormSection
            title="Benefits"
            description="Set the additional Minecraft homes granted by this role."
        >
            <FormField
                id="additional_homes"
                label="Additional homes"
                description="Number of extra homes granted to members of this role."
                :error="form.errors.additional_homes"
                v-slot="field"
            >
                <Input v-bind="field" v-model="form.additional_homes" type="number" min="0" step="1" />
            </FormField>
        </FormSection>

        <FormSection
            title="Display Settings"
            description="Control which role is displayed when a player has several roles of the same type."
        >
            <FormField
                id="display_priority"
                label="Display priority"
                description="The role with the highest priority is displayed. Use a unique value among roles of the same type."
                :error="form.errors.display_priority"
                v-slot="field"
            >
                <Input v-bind="field" v-model="form.display_priority" type="number" step="1" />
            </FormField>
        </FormSection>

        <FormActions
            :submit-label="isEdit ? 'Update role' : 'Create role'"
            :cancel-href="'/manage/roles'"
            :processing="form.processing"
            sticky
        />

        <DangerZone
            v-if="isEdit"
            title="Delete role"
            description="Permanently remove this role. This does not perform any other account or permission changes."
            confirm-message="This cannot be undone. Are you sure you want to delete this role?"
            confirm-title="Yes, delete role"
            :disabled="form.processing"
            @confirm="destroy"
        />
    </form>
</template>
