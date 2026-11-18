<script setup lang="ts">
import ManageFormPage from '../../Components/ManageFormPage.vue'
import PlayerForm from './Partials/PlayerForm.vue'

const props = defineProps<AdminPageProps<'manage/Players/PlayerEdit'>>()

function submit(form: Parameters<InstanceType<typeof PlayerForm>['$props']['submit']>[0]) {
    form.put('/manage/players/' + props.player.id)
}
</script>

<template>
    <ManageFormPage title="Edit Player" description="Update this Minecraft player's registered details.">
        <PlayerForm :player="props.player" :submit="submit" />
    </ManageFormPage>
</template>
