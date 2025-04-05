<script setup lang="ts">
import { Head, InertiaForm } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import DonationForm from './Partials/DonationForm.vue'
import BackButton from '../../Components/BackButton.vue'
import { Donation } from '../../Data/Donation'

function submit(form: InertiaForm<Donation>) {
    form.post('/manage/donations')
}
</script>

<template>
    <section>
        <Head title="Create a Donation"/>

        <Card>
            <div class="p-8 max-w-2xl">
                <BackButton href="/manage/donations" class="mb-4"/>

                <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Create a Donation</h2>
                <div class="text-sm text-gray-500">For book-keeping, and adds an amount to the "donation bar"</div>
                <hr class="my-6"/>

                <DonationForm :submit="submit"/>
            </div>
        </Card>
    </section>
</template>
