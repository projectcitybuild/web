<script setup lang="ts">
import ManageFormPage from '../../../../Components/ManageFormPage.vue'
import DonationForm from './Partials/DonationForm.vue'

function submit(form: Parameters<InstanceType<typeof DonationForm>['$props']['submit']>[0]) {
    form.post('/manage/donations')
}
</script>

<template>
    <ManageFormPage title="Create Donation" description="Record a contribution for bookkeeping and the donation bar.">
        <DonationForm :submit="submit" />
    </ManageFormPage>
</template>
