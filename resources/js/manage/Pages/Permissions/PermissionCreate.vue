<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import PermissionForm from './Partials/PermissionForm.vue'
import { Permission } from '../../Data/Permission'

function submit(form: InertiaForm<Permission>) {
    form.post('/manage/permissions')
}
</script>

<template>
    <ManageFormPage title="Create Permission" description="Define a permission that can be assigned to a role.">
        <PermissionForm :submit="submit" />
    </ManageFormPage>
</template>
