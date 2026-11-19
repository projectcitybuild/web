<script setup lang="ts">
import { Routes } from '../../../../Navigation/Routes'
import ManageFormPage from '../../../../Components/ManageFormPage.vue'
import DonationPerkForm from './Partials/DonationPerkForm.vue'

const props = defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/DonationPerks/DonationPerkEdit']>()

function submit(form: Parameters<InstanceType<typeof DonationPerkForm>['$props']['submit']>[0]) {
    form.put(Routes.manage.donationPerks.update(props.perk.id))
}
</script>

<template>
    <ManageFormPage title="Edit Donation Perk" description="Update the account benefit and its status.">
        <DonationPerkForm :perk="props.perk" :tiers="props.tiers" :submit="submit" />
    </ManageFormPage>
</template>
