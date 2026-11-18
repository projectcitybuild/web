<script setup lang="ts">
import ManageFormPage from '../../Components/ManageFormPage.vue'
import BadgeForm from './Partials/BadgeForm.vue'

function submit(form: Parameters<InstanceType<typeof BadgeForm>['$props']['submit']>[0]) {
    form.post('/manage/badges')
}
</script>

<template>
    <ManageFormPage title="Create Badge" description="Create a player accolade that can be displayed publicly.">
        <BadgeForm :submit="submit" />
    </ManageFormPage>
</template>
