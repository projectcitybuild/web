<script setup lang="ts">
import ManageFormPage from '../../Components/ManageFormPage.vue'
import BanForm from './Partials/IPBanForm.vue'

const props = defineProps<AdminPageProps<'manage/IPBans/IPBanEdit'>>()

function submit(form: Parameters<InstanceType<typeof BanForm>['$props']['submit']>[0]) {
    form.put('/manage/ip-bans/' + props.ban.id)
}
</script>

<template>
    <ManageFormPage title="Edit IP Ban" description="Update this IP address restriction; IP bans are unreliable and should be a last resort.">
        <BanForm :ban="props.ban" :submit="submit" />
    </ManageFormPage>
</template>
