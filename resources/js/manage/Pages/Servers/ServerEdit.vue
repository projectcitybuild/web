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
    <ManageFormPage title="Edit Server" description="Update this server's registration and integration settings." :breadcrumbs="[{ label: 'Manage', href: '/manage' }, { label: 'Servers', href: '/manage/servers' }, { label: 'Edit' }]">
        <ServerForm :server="props.server" :submit="submit" />
    </ManageFormPage>
</template>
