<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import ServerForm from './Partials/ServerForm.vue'
import { Server } from '../../Data/Server'

function submit(form: InertiaForm<Server>) {
    form.post('/manage/servers')
}
</script>

<template>
    <ManageFormPage title="Create a Server" description="Registers a server for receiving webhooks and events">
        <ServerForm :submit="submit" />
    </ManageFormPage>
</template>
