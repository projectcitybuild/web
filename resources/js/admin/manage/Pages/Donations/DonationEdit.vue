<script setup lang="ts">
import ManageFormPage from '../../Components/ManageFormPage.vue'
import DonationForm from './Partials/DonationForm.vue'

const props = defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/Donations/DonationEdit']>()

function submit(form: Parameters<InstanceType<typeof DonationForm>['$props']['submit']>[0]) {
    form.put('/manage/donations/' + props.donation.id)
}
</script>

<template>
    <ManageFormPage title="Edit Donation" description="Update this contribution record and its donation-bar amount.">
        <DonationForm :donation="props.donation" :submit="submit" />
    </ManageFormPage>
</template>
