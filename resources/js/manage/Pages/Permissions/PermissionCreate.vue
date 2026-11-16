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
    <ManageFormPage title="Create a Permission" description="Permissions allow a role access to a resource">
        <PermissionForm :submit="submit" />
    </ManageFormPage>
</template>
