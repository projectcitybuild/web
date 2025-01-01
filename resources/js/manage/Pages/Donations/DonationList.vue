<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { Paginated } from '../../Data/Paginated'
import type { Donation } from '../../Data/Donation'
import DonationListTable from './Partials/DonationListTable.vue'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import Card from '../../Components/Card.vue'

interface Props {
    success?: string,
    donations: Paginated<Donation>,
}

defineProps<Props>()
</script>

<template>
    <Head title="Manage Donations"/>

    <section class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5">
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <div class="mx-auto max-w-screen-xl">
            <Card class="overflow-hidden">
                <div
                    class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                    <div class="w-full md:w-1/2">

                    </div>
                    <div>
                        <Link
                            href="/manage/donations/create"
                            as="button"
                            class="
                                flex flex-row items-center justify-center px-4 py-2 rounded-lg
                                text-sm text-white bg-blue-700
                                hover:bg-primary-800
                                focus:ring-4 focus:ring-blue-300
                                dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800
                            "
                        >
                            <svg class="size-6 mr-2" fill="currentColor" viewbox="0 0 20 20"
                                 xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path clip-rule="evenodd" fill-rule="evenodd"
                                      d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/>
                            </svg>
                            Create Donation
                        </Link>
                    </div>
                </div>

                <InfinitePagination
                    :initial="donations"
                    v-slot="source"
                    class="overflow-x-auto"
                >
                    <DonationListTable :donations="source.data"/>
                </InfinitePagination>
            </Card>
        </div>
    </section>
</template>
