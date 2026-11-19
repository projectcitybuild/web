<script setup lang="ts">
import ManageFormPage from '../../../../Components/ManageFormPage.vue'
import PermissionForm from './Partials/PermissionForm.vue'

const props = defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/Permissions/PermissionEdit']>()

function submit(form: Parameters<InstanceType<typeof PermissionForm>['$props']['submit']>[0]) {
    form.put('/manage/permissions/' + props.permission.id)
}
</script>

<template>
    <ManageFormPage title="Edit Permission" description="Update this permission and the resource access it represents.">
        <PermissionForm :permission="props.permission" :submit="submit" />
    </ManageFormPage>
</template>
