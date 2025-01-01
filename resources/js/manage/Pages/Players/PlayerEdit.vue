<script setup lang="ts">
import { Head, InertiaForm } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import BackButton from '../../Components/BackButton.vue'
import type { Donation } from '../../Data/Donation'
import DonationForm from './Partials/PlayerForm.vue'

interface Props {
    donation: Donation,
}

const props = defineProps<Props>()

function submit(form: InertiaForm<Donation>) {
    form.put('/manage/donations/' + props.donation.donation_id)
}
</script>

<template>
    <section class="p-3 sm:p-5">
        <Head title="Edit a Badge"/>

        <div class="mx-auto max-w-screen-xl">
            <Card>
                <div class="p-8 max-w-2xl">
                    <BackButton href="/manage/donations" class="mb-4"/>

                    <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Edit a Donation</h2>
                    <div class="text-sm text-gray-500">For book-keeping, and adds an amount to the "donation bar"</div>
                    <hr class="my-6"/>

                    <DonationForm :donation="props.donation" :submit="submit"/>
                </div>
            </Card>
        </div>
    </section>
</template>
