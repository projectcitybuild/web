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
    <ManageFormPage title="Edit an IP Ban" description="Prevent an IP address from connecting to our server. This should be a last resort as IP address bans are extremely unreliable">
        <BanForm :ban="props.ban" :submit="submit" />
    </ManageFormPage>
</template>
