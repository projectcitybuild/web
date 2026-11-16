<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import WarningForm from './Partials/WarningForm.vue'
import type { PlayerWarning } from '../../Data/PlayerWarning'

interface Props {
    warning: PlayerWarning,
}

const props = defineProps<Props>()

function submit(form: InertiaForm<PlayerWarning>) {
    form.put('/manage/warnings/' + props.warning.id)
}
</script>

<template>
    <ManageFormPage title="Edit a Player Warning" description="Record an infraction against a player">
        <WarningForm :warning="props.warning" :submit="submit" />
    </ManageFormPage>
</template>
