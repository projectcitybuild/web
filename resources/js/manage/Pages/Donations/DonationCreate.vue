<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import DonationForm from './Partials/DonationForm.vue'
import { Donation } from '../../Data/Donation'

function submit(form: InertiaForm<Donation>) {
    form.post('/manage/donations')
}
</script>

<template>
    <ManageFormPage title="Create Donation" description="Record a contribution for bookkeeping and the donation bar." :breadcrumbs="[{ label: 'Manage', href: '/manage' }, { label: 'Donations', href: '/manage/donations' }, { label: 'Create' }]">
        <DonationForm :submit="submit" />
    </ManageFormPage>
</template>
