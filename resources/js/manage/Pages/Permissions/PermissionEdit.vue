<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import { Permission } from '../../Data/Permission';
import PermissionForm from './Partials/PermissionForm.vue';

interface Props {
    permission: Permission,
}

const props = defineProps<Props>()

function submit(form: InertiaForm<Permission>) {
    form.put('/manage/permissions/' + props.permission.id)
}
</script>

<template>
    <ManageFormPage title="Edit a Permission" description="Permissions allow a role access to a resource">
        <PermissionForm :permission="props.permission" :submit="submit" />
    </ManageFormPage>
</template>
