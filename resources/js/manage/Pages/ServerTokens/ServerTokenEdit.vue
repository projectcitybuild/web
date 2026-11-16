<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import ServerTokenForm from './Partials/ServerTokenForm.vue'
import type { ServerToken } from '../../Data/ServerToken'

interface Props {
    token: ServerToken,
}

const props = defineProps<Props>()

function submit(form: InertiaForm<ServerToken>) {
    form.put('/manage/server-tokens/' + props.token.id)
}
</script>

<template>
    <ManageFormPage title="Edit Server Token" description="Update this API credential's name and access settings." :breadcrumbs="[{ label: 'Manage', href: '/manage' }, { label: 'Servers', href: '/manage/servers' }, { label: 'Tokens', href: '/manage/server-tokens' }, { label: 'Edit' }]">
        <ServerTokenForm :token="props.token" :submit="submit" />
    </ManageFormPage>
</template>
