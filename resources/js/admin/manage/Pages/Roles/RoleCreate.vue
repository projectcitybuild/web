<script setup lang="ts">
import ManageFormPage from '../../Components/ManageFormPage.vue'
import RoleForm from './Partials/RoleForm.vue'

function submit(form: Parameters<InstanceType<typeof RoleForm>['$props']['submit']>[0]) {
    form.post('/manage/roles')
}
</script>

<template>
    <ManageFormPage title="Create Role" description="Create a role to group members and grant application permissions.">
        <RoleForm :submit="submit" />
    </ManageFormPage>
</template>
