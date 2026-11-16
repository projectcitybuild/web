<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import RoleForm from './Partials/RoleForm.vue'
import type { Role } from '../../Data/Role'

interface Props {
    role: Role,
}

const props = defineProps<Props>()

function submit(form: InertiaForm<Role>) {
    form.put('/manage/roles/' + props.role.id)
}
</script>

<template>
    <ManageFormPage title="Edit Role" description="Update this role and the permissions assigned to its members." :breadcrumbs="[{ label: 'Manage', href: '/manage' }, { label: 'Roles', href: '/manage/roles' }, { label: 'Edit' }]">
        <RoleForm :role="props.role" :submit="submit" />
    </ManageFormPage>
</template>
