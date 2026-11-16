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
    <ManageFormPage title="Create a Role" description="Roles are a collection of members with granted permissions">
        <RoleForm :submit="submit" />
    </ManageFormPage>
</template>
