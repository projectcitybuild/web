<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import { Warp } from '../../Data/Warp'
import HomeForm from './Partials/HomeForm.vue'

function submit(form: InertiaForm<Warp>) {
    form.post('/manage/homes')
}
</script>

<template>
    <ManageFormPage title="Create Home" description="Register a saved location for a Minecraft player." :breadcrumbs="[{ label: 'Manage', href: '/manage' }, { label: 'Minecraft' }, { label: 'Homes', href: '/manage/homes' }, { label: 'Create' }]">
        <HomeForm :submit="submit" />
    </ManageFormPage>
</template>
