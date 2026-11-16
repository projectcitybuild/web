<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import WarpForm from './Partials/WarpForm.vue'
import { Warp } from '../../Data/Warp'

function submit(form: InertiaForm<Warp>) {
    form.post('/manage/minecraft/warps')
}
</script>

<template>
    <ManageFormPage title="Create Warp" description="Register a location that Minecraft players can teleport to.">
        <WarpForm :submit="submit" />
    </ManageFormPage>
</template>
