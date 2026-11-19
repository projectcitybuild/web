<script setup lang="ts">
import ManageFormPage from '../../../../Components/ManageFormPage.vue'
import PlayerForm from './Partials/PlayerForm.vue'

function submit(form: Parameters<InstanceType<typeof PlayerForm>['$props']['submit']>[0]) {
    form.post('/manage/players')
}
</script>

<template>
    <ManageFormPage title="Create Player" description="Register a Minecraft UUID that has connected to the server.">
        <PlayerForm :submit="submit" />
    </ManageFormPage>
</template>
