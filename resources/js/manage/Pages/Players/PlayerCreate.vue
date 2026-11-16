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
    <ManageFormPage title="Create Player" description="Register a Minecraft UUID that has connected to the server." :breadcrumbs="[{ label: 'Manage', href: '/manage' }, { label: 'Minecraft' }, { label: 'Players', href: '/manage/players' }, { label: 'Create' }]">
        <PlayerForm :submit="submit" />
    </ManageFormPage>
</template>
