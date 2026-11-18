<script setup lang="ts">
import ManageFormPage from '../../Components/ManageFormPage.vue'
import WarpForm from './Partials/WarpForm.vue'

function submit(form: Parameters<InstanceType<typeof WarpForm>['$props']['submit']>[0]) {
    form.post('/manage/minecraft/warps')
}
</script>

<template>
    <ManageFormPage title="Create Warp" description="Register a location that Minecraft players can teleport to.">
        <WarpForm :submit="submit" />
    </ManageFormPage>
</template>
