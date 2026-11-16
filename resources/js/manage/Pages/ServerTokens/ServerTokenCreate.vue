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
    <ManageFormPage title="Create a Server Token" description="Creates an authorization token that grants access to the PCB API. For use with first-party applications only">
        <ServerTokenForm :submit="submit" />
    </ManageFormPage>
</template>
