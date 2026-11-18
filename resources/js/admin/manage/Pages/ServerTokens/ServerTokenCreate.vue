<script setup lang="ts">
import ManageFormPage from '../../Components/ManageFormPage.vue'
import ServerTokenForm from './Partials/ServerTokenForm.vue'

function submit(form: Parameters<InstanceType<typeof ServerTokenForm>['$props']['submit']>[0]) {
    form.post('/manage/server-tokens')
}
</script>

<template>
    <ManageFormPage title="Create Server Token" description="Create an API credential for a trusted first-party application.">
        <ServerTokenForm :submit="submit" />
    </ManageFormPage>
</template>
