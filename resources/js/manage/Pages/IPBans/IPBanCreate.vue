<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import BanForm from './Partials/IPBanForm.vue'
import { IPBan } from '../../Data/IPBan'

function submit(form: InertiaForm<IPBan>) {
    form.post('/manage/ip-bans')
}
</script>

<template>
    <ManageFormPage title="Create IP Ban" description="Restrict an IP address from connecting to the server; IP bans are unreliable and should be a last resort." :breadcrumbs="[{ label: 'Manage', href: '/manage' }, { label: 'Moderation' }, { label: 'IP Bans', href: '/manage/ip-bans' }, { label: 'Create' }]">
        <BanForm :submit="submit" />
    </ManageFormPage>
</template>
