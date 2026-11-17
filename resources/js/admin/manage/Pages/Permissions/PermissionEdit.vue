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
    <ManageFormPage title="Edit Permission" description="Update this permission and the resource access it represents.">
        <PermissionForm :permission="props.permission" :submit="submit" />
    </ManageFormPage>
</template>
