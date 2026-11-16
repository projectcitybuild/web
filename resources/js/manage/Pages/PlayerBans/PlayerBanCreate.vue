<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import BanForm from './Partials/PlayerBanForm.vue'
import type { PlayerBan } from '../../Data/PlayerBan'

function submit(form: InertiaForm<PlayerBan>) {
    form.post('/manage/player-bans')
}
</script>

<template>
    <ManageFormPage title="Create Player Ban" description="Prevent a Minecraft player UUID from connecting to the server." :breadcrumbs="[{ label: 'Manage', href: '/manage' }, { label: 'Moderation' }, { label: 'Player Bans', href: '/manage/player-bans' }, { label: 'Create' }]">
        <BanForm :submit="submit" />
    </ManageFormPage>
</template>
