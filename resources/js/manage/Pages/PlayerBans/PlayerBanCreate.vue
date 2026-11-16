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
    <ManageFormPage title="Create a Player Ban" description="Prevent a Minecraft UUID from connecting to our server">
        <BanForm :submit="submit" />
    </ManageFormPage>
</template>
