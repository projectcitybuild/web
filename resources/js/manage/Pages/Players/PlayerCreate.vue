<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import PlayerForm from './Partials/PlayerForm.vue'
import type { Player } from '../../Data/Player'

function submit(form: InertiaForm<Player>) {
    form.post('/manage/players')
}
</script>

<template>
    <ManageFormPage title="Create a Player" description="A Minecraft UUID that has connected to our server before">
        <PlayerForm :submit="submit" />
    </ManageFormPage>
</template>
