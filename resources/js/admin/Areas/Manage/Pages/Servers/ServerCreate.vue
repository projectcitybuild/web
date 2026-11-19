<script setup lang="ts">
import ManageFormPage from '../../../../Components/ManageFormPage.vue'
import ServerForm from './Partials/ServerForm.vue'

function submit(form: Parameters<InstanceType<typeof ServerForm>['$props']['submit']>[0]) {
    form.post('/manage/servers')
}
</script>

<template>
    <ManageFormPage title="Create Server" description="Register a server to receive webhooks and events.">
        <ServerForm :submit="submit" />
    </ManageFormPage>
</template>
