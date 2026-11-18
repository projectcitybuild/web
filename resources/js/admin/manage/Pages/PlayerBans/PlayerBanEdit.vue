<script setup lang="ts">
import ManageFormPage from '../../Components/ManageFormPage.vue'
import BanForm from './Partials/PlayerBanForm.vue'
import type { PlayerBan } from '../../Data/PlayerBan'

interface Props {
    ban: PlayerBan,
}

const props = defineProps<Props>()

function submit(form: Parameters<InstanceType<typeof BanForm>['$props']['submit']>[0]) {
    form.put('/manage/player-bans/' + props.ban.id)
}

</script>

<template>
    <ManageFormPage title="Edit Player Ban" description="Update this Minecraft player ban and its restrictions.">
        <BanForm :ban="props.ban" :submit="submit" />
    </ManageFormPage>
</template>
