<script setup lang="ts">
import { Routes } from '../../../../Navigation/Routes'
import { Deferred, Head } from '@inertiajs/vue3'
import DonationListTable from './Partials/DonationListTable.vue'
import InfinitePagination from '../../../../Components/InfinitePagination.vue'
import SuccessAlert from '../../../../Components/SuccessAlert.vue'
import SvgIcon from '../../../../Components/SvgIcon.vue'
import SpinnerRow from '../../../../Components/SpinnerRow.vue'
import { ref } from 'vue'
import { Icons } from '../../../../Components/SvgIcon.vue'
import PageHeader from '../../../../Components/PageHeader.vue'
import LinkButton from '../../../../Components/LinkButton.vue'
import ListPage from '../../../../Components/ListPage.vue'

defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/Donations/DonationList']>()

const itemCount = ref(0)
</script>

<template>
    <Head title="Manage Donations"/>

    <div class="space-y-6">
        <PageHeader title="Donations" description="Review and manage donation records.">
            <template #actions>
                <LinkButton :href="Routes.manage.donations.create" variant="primary">
                    <SvgIcon :svg="Icons.plus" />
                    Create Donation
                </LinkButton>
            </template>
        </PageHeader>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <ListPage
            :count="donations ? itemCount : undefined"
            :total="donations?.total"
            count-label="donations"
        >
            <Deferred data="donations">
                <template #fallback>
                    <SpinnerRow />
                </template>

                <InfinitePagination
                    :initial="donations"
                    :sort="sort"
                    :direction="direction"
                    v-model:count="itemCount"
                    v-slot="source"
                >
                    <DonationListTable
                        :donations="source.data"
                        :sort="source.sort"
                        :direction="source.direction"
                        :on-sort="source.onSort"
                    />
                </InfinitePagination>
            </Deferred>
        </ListPage>
    </section>
    </div>
</template>
