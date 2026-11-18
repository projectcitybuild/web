<script setup lang="ts">
import { InertiaForm, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import type { Permission } from '../../../Data/Permission'
import DangerZone from '../../../Components/Forms/DangerZone.vue'
import FormActions from '../../../Components/Forms/FormActions.vue'
import FormErrorSummary from '../../../Components/Forms/FormErrorSummary.vue'
import FormField from '../../../Components/Forms/FormField.vue'
import FormSection from '../../../Components/Forms/FormSection.vue'
import Input from '../../../../shared/Components/Input.vue'
import { useUnsavedChangesWarning } from '../../../Components/Forms/useUnsavedChangesWarning'

interface Props {
    permission?: Permission,
    submit: (form: InertiaForm<PermissionFormData>) => void,
}

interface PermissionFormData {
    name: string,
}

const props = defineProps<Props>()

const form = useForm<PermissionFormData>({
    name: props.permission?.name ?? '',
})

const isEdit = computed(() => props.permission != null)
useUnsavedChangesWarning(form)

function submit(): void {
    props.submit(form)
}

function destroy(): void {
    if (props.permission) {
        form.delete(`/manage/permissions/${props.permission.id}`)
    }
}
</script>

<template>
    <form class="grid gap-6" @submit.prevent="submit">
        <FormErrorSummary :errors="form.errors" />

        <FormSection
            title="Permission Details"
            description="Set the permission name used by the server."
        >
            <FormField
                id="name"
                label="Name"
                :error="form.errors.name"
                required
                v-slot="field"
            >
                <Input v-bind="field" v-model="form.name" />
            </FormField>
        </FormSection>

        <FormActions
            :submit-label="isEdit ? 'Update permission' : 'Create permission'"
            cancel-href="/manage/permissions"
            :processing="form.processing"
            sticky
        />

        <DangerZone
            v-if="isEdit"
            title="Delete permission"
            description="Permanently remove this permission."
            confirm-message="This cannot be undone."
            confirm-title="Yes, Delete It"
            :disabled="form.processing"
            @confirm="destroy"
        />
    </form>
</template>
