<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import RoleForm from './Partials/RoleForm.vue'
import { Role } from '../../Data/Role'

function submit(form: InertiaForm<Role>) {
    form.post('/manage/roles')
}
</script>

<template>
    <ManageFormPage title="Create Role" description="Create a role to group members and grant application permissions." :breadcrumbs="[{ label: 'Manage', href: '/manage' }, { label: 'Roles', href: '/manage/roles' }, { label: 'Create' }]">
        <RoleForm :submit="submit" />
    </ManageFormPage>
</template>
