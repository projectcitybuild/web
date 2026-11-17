<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import ServerTokenForm from './Partials/ServerTokenForm.vue'
import { ServerToken } from '../../Data/ServerToken'

function submit(form: InertiaForm<ServerToken>) {
    form.post('/manage/server-tokens')
}
</script>

<template>
    <ManageFormPage title="Create Server Token" description="Create an API credential for a trusted first-party application.">
        <ServerTokenForm :submit="submit" />
    </ManageFormPage>
</template>
