<script setup lang="ts">
import ManageFormPage from '../../Components/ManageFormPage.vue'
import type { Donation } from '../../Data/Donation'
import DonationForm from './Partials/DonationForm.vue'

interface Props {
    donation: Donation,
}

const props = defineProps<Props>()

function submit(form: Parameters<InstanceType<typeof DonationForm>['$props']['submit']>[0]) {
    form.put('/manage/donations/' + props.donation.id)
}
</script>

<template>
    <ManageFormPage title="Edit Donation" description="Update this contribution record and its donation-bar amount.">
        <DonationForm :donation="props.donation" :submit="submit" />
    </ManageFormPage>
</template>
