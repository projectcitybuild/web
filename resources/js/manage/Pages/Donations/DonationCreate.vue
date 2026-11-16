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
    <ManageFormPage title="Create a Donation" description="For book-keeping, and adds an amount to the &quot;donation bar&quot;">
        <DonationForm :submit="submit" />
    </ManageFormPage>
</template>
