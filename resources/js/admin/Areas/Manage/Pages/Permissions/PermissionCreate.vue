<script setup lang="ts">
import ManageFormPage from '../../../../Components/ManageFormPage.vue'
import PermissionForm from './Partials/PermissionForm.vue'

function submit(form: Parameters<InstanceType<typeof PermissionForm>['$props']['submit']>[0]) {
    form.post('/manage/permissions')
}
</script>

<template>
    <ManageFormPage title="Create Permission" description="Define a permission that can be assigned to a role.">
        <PermissionForm :submit="submit" />
    </ManageFormPage>
</template>
