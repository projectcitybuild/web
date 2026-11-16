<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import BanForm from './Partials/PlayerBanForm.vue'
import { PlayerBan } from '../../Data/PlayerBan'

interface Props {
    ban: PlayerBan,
}

const props = defineProps<Props>()

function submit(form: InertiaForm<PlayerBan>) {
    form.put('/manage/player-bans/' + props.ban.id)
}

</script>

<template>
    <ManageFormPage title="Edit Player Ban" description="Update this Minecraft player ban and its restrictions." :breadcrumbs="[{ label: 'Manage', href: '/manage' }, { label: 'Moderation' }, { label: 'Player Bans', href: '/manage/player-bans' }, { label: 'Edit' }]">
        <BanForm :ban="props.ban" :submit="submit" />
    </ManageFormPage>
</template>
