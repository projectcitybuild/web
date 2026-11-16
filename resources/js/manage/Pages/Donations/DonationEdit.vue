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
    <ManageFormPage title="Edit a Donation" description="For book-keeping, and adds an amount to the &quot;donation bar&quot;">
        <DonationForm :donation="props.donation" :submit="submit" />
    </ManageFormPage>
</template>
