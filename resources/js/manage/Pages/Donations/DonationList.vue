<script setup lang="ts">
import { Deferred, Head, Link } from '@inertiajs/vue3'
import { Paginated } from '../../Data/Paginated'
import type { Donation } from '../../Data/Donation'
import DonationListTable from './Partials/DonationListTable.vue'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import Card from '../../Components/Card.vue'
import FilledButton from '../../Components/FilledButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import SpinnerRow from '../../Components/SpinnerRow.vue'
import { ref } from 'vue'
import { Icons } from '../../Icons'
import ShowingCount from '../../Components/ShowingCount.vue'
import PageHeader from '../../../shared/Components/PageHeader.vue'
import ManageBreadcrumbs from '../../Components/ManageBreadcrumbs.vue'
import LinkButton from '../../../shared/Components/LinkButton.vue'

interface Props {
    success?: string,
    donations?: Paginated<Donation>,
}
defineProps<Props>()

const itemCount = ref(0)
</script>

<template>
    <Head title="Manage Donations"/>

    <div class="space-y-6">
        <PageHeader title="Donations" description="Review and manage donation records.">
            <template #breadcrumbs>
                <ManageBreadcrumbs :items="[{ label: 'Manage', href: '/manage' }, { label: 'Donations' }]" />
            </template>
            <template #actions>
                <LinkButton href="/manage/donations/create" variant="primary">
                    <SvgIcon :svg="Icons.plus" />
                    Create Donation
                </LinkButton>
            </template>
        </PageHeader>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <Card padding="none">
            <div class="flex flex-row items-center justify-between py-4">
                <div>
                    <ShowingCount
                        v-if="donations"
                        :from="itemCount > 0 ? 1 : 0"
                        :to="itemCount"
                        :total="donations.total"
                        class="text-sm text-muted"
                    />
                </div>

            </div>

            <Deferred data="donations">
                <template #fallback>
                    <SpinnerRow />
                </template>

                <InfinitePagination
                    :initial="donations"
                    v-model:count="itemCount"
                    v-slot="source"
                >
                    <DonationListTable :donations="source.data"/>
                </InfinitePagination>
            </Deferred>
        </Card>
    </section>
    </div>
</template>
