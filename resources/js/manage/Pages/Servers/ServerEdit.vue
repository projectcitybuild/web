<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import ServerForm from './Partials/ServerForm.vue'
import type { Server } from '../../Data/Server'

interface Props {
    server: Server,
}

const props = defineProps<Props>()

function submit(form: InertiaForm<Server>) {
    form.put('/manage/servers/' + props.server.id)
}
</script>

<template>
    <ManageFormPage title="Edit a Server" description="Registers a server for receiving webhooks and events">
        <ServerForm :server="props.server" :submit="submit" />
    </ManageFormPage>
</template>
