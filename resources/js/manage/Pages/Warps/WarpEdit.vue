<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import WarpForm from './Partials/WarpForm.vue'
import type { Warp } from '../../Data/Warp'

interface Props {
    warp: Warp,
}

const props = defineProps<Props>()

function submit(form: InertiaForm<Warp>) {
    form.put('/manage/minecraft/warps/' + props.warp.id)
}
</script>

<template>
    <ManageFormPage title="Edit a Warp" description="Register a location players can freely teleport to">
        <WarpForm :warp="props.warp" :submit="submit" />
    </ManageFormPage>
</template>
