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
    <ManageFormPage title="Edit a Role" description="Roles are a collection of members with granted permissions">
        <RoleForm :role="props.role" :submit="submit" />
    </ManageFormPage>
</template>
