<script setup lang="ts">
import ManageFormPage from '../../Components/ManageFormPage.vue'
import HomeForm from './Partials/HomeForm.vue'

function submit(form: Parameters<InstanceType<typeof HomeForm>['$props']['submit']>[0]) {
    form.post('/manage/homes')
}
</script>

<template>
    <ManageFormPage title="Create Home" description="Register a saved location for a Minecraft player.">
        <HomeForm :submit="submit" />
    </ManageFormPage>
</template>
