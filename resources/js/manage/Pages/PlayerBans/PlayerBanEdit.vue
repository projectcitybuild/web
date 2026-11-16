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
    <ManageFormPage title="Edit a Player Ban" description="Prevent a Minecraft UUID from connecting to our server">
        <BanForm :ban="props.ban" :submit="submit" />
    </ManageFormPage>
</template>
