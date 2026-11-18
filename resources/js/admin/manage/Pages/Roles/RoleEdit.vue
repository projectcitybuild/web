<script setup lang="ts">
import ManageFormPage from '../../Components/ManageFormPage.vue'
import RoleForm from './Partials/RoleForm.vue'

const props = defineProps<AdminPageProps<'manage/Roles/RoleEdit'>>()

function submit(form: Parameters<InstanceType<typeof RoleForm>['$props']['submit']>[0]) {
    form.put('/manage/roles/' + props.role.id)
}
</script>

<template>
    <ManageFormPage title="Edit Role" description="Update this role and the permissions assigned to its members.">
        <RoleForm :role="props.role" :submit="submit" />
    </ManageFormPage>
</template>
