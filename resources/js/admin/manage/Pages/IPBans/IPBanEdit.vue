<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import BanForm from './Partials/IPBanForm.vue'
import { IPBan } from '../../Data/IPBan'

interface Props {
    ban: IPBan,
}

const props = defineProps<Props>()

function submit(form: InertiaForm<IPBan>) {
    form.put('/manage/ip-bans/' + props.ban.id)
}
</script>

<template>
    <ManageFormPage title="Edit IP Ban" description="Update this IP address restriction; IP bans are unreliable and should be a last resort.">
        <BanForm :ban="props.ban" :submit="submit" />
    </ManageFormPage>
</template>
