<script setup lang="ts">
import ManageFormPage from '../../Components/ManageFormPage.vue'
import WarningForm from './Partials/WarningForm.vue'

const props = defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/Warnings/WarningEdit']>()

function submit(form: Parameters<InstanceType<typeof WarningForm>['$props']['submit']>[0]) {
    form.put('/manage/warnings/' + props.warning.id)
}
</script>

<template>
    <ManageFormPage title="Edit Warning" description="Update this recorded player infraction.">
        <WarningForm :warning="props.warning" :submit="submit" />
    </ManageFormPage>
</template>
