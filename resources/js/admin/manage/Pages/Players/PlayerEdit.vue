<script setup lang="ts">
import ManageFormPage from '../../Components/ManageFormPage.vue'
import PlayerForm from './Partials/PlayerForm.vue'
import type { Player } from '../../Data/Player'

interface Props {
    player: Player,
}

const props = defineProps<Props>()

function submit(form: Parameters<InstanceType<typeof PlayerForm>['$props']['submit']>[0]) {
    form.put('/manage/players/' + props.player.id)
}
</script>

<template>
    <ManageFormPage title="Edit Player" description="Update this Minecraft player's registered details.">
        <PlayerForm :player="props.player" :submit="submit" />
    </ManageFormPage>
</template>
