<script setup lang="ts">
import { Routes } from '../../../../Navigation/Routes'
import ManageFormPage from '../../../../Components/ManageFormPage.vue'
import DonationPerkForm from './Partials/DonationPerkForm.vue'

const props = defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/DonationPerks/DonationPerkCreate']>()

function submit(form: Parameters<InstanceType<typeof DonationPerkForm>['$props']['submit']>[0]) {
    form.post(Routes.manage.donationPerks.list)
}
</script>

<template>
    <ManageFormPage title="Create Donation Perk" description="Grant a donation benefit to an account.">
        <DonationPerkForm :tiers="props.tiers" :submit="submit" />
    </ManageFormPage>
</template>
