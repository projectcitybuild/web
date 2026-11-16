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
    <ManageFormPage title="Create Server" description="Register a server to receive webhooks and events." :breadcrumbs="[{ label: 'Manage', href: '/manage' }, { label: 'Servers', href: '/manage/servers' }, { label: 'Create' }]">
        <ServerForm :submit="submit" />
    </ManageFormPage>
</template>
