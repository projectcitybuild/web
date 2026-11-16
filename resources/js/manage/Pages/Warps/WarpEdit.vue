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
    <ManageFormPage title="Edit Warp" description="Update this Minecraft teleport location." :breadcrumbs="[{ label: 'Manage', href: '/manage' }, { label: 'Minecraft' }, { label: 'Warps', href: '/manage/minecraft/warps' }, { label: 'Edit' }]">
        <WarpForm :warp="props.warp" :submit="submit" />
    </ManageFormPage>
</template>
