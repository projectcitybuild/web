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
    <ManageFormPage title="Create Server Token" description="Create an API credential for a trusted first-party application." :breadcrumbs="[{ label: 'Manage', href: '/manage' }, { label: 'Servers', href: '/manage/servers' }, { label: 'Tokens', href: '/manage/server-tokens' }, { label: 'Create' }]">
        <ServerTokenForm :submit="submit" />
    </ManageFormPage>
</template>
