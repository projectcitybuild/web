<script setup lang="ts">
import ManageFormPage from '../../../../Components/ManageFormPage.vue'
import WarningForm from './Partials/WarningForm.vue'

function submit(form: Parameters<InstanceType<typeof WarningForm>['$props']['submit']>[0]) {
    form.post('/manage/warnings')
}
</script>

<template>
    <ManageFormPage title="Create Warning" description="Record an infraction against a Minecraft player.">
        <WarningForm :submit="submit" />
    </ManageFormPage>
</template>
