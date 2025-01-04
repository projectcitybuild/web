<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { Paginated } from '../../Data/Paginated'
import type { Donation } from '../../Data/Donation'
import DonationListTable from './Partials/DonationListTable.vue'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import Card from '../../Components/Card.vue'
import FilledButton from '../../Components/FilledButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'

interface Props {
    success?: string,
    donations: Paginated<Donation>,
}

defineProps<Props>()
</script>

<template>
    <Head title="Manage Donations"/>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <div class="mx-auto max-w-screen-xl">
            <Card class="overflow-hidden">
                <div
                    class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                    <div class="w-full md:w-1/2">

                    </div>
                    <div>
                        <Link href="/manage/donations/create">
                            <FilledButton variant="primary">
                                <SvgIcon icon="plus" />
                                Create Donation
                            </FilledButton>
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
