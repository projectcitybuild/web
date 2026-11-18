<script setup lang="ts">
import ManageFormPage from '../../Components/ManageFormPage.vue'
import BanForm from './Partials/IPBanForm.vue'

function submit(form: Parameters<InstanceType<typeof BanForm>['$props']['submit']>[0]) {
    form.post('/manage/ip-bans')
}
</script>

<template>
    <ManageFormPage title="Create IP Ban" description="Restrict an IP address from connecting to the server; IP bans are unreliable and should be a last resort.">
        <BanForm :submit="submit" />
    </ManageFormPage>
</template>
