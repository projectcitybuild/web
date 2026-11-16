<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import BanForm from './Partials/IPBanForm.vue'
import { IPBan } from '../../Data/IPBan'

function submit(form: InertiaForm<IPBan>) {
    form.post('/manage/ip-bans')
}
</script>

<template>
    <ManageFormPage title="Create an IP Ban" description="Prevent an IP address from connecting to our server. This should be a last resort as IP address bans are extremely unreliable">
        <BanForm :submit="submit" />
    </ManageFormPage>
</template>
