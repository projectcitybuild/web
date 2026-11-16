<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import type { Donation } from '../../Data/Donation'
import DonationForm from './Partials/DonationForm.vue'

interface Props {
    donation: Donation,
}

const props = defineProps<Props>()

function submit(form: InertiaForm<Donation>) {
    form.put('/manage/donations/' + props.donation.id)
}
</script>

<template>
    <ManageFormPage title="Edit Donation" description="Update this contribution record and its donation-bar amount." :breadcrumbs="[{ label: 'Manage', href: '/manage' }, { label: 'Donations', href: '/manage/donations' }, { label: 'Edit' }]">
        <DonationForm :donation="props.donation" :submit="submit" />
    </ManageFormPage>
</template>
