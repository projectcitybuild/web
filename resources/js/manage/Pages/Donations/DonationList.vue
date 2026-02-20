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

interface Props {
    success?: string,
    donations?: Paginated<Donation>,
}
defineProps<Props>()

const itemCount = ref(0)
</script>

<template>
    <Head title="Manage Donations"/>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <Card>
            <div class="flex flex-row items-center justify-between p-4">
                <div>
                    <span v-if="donations" class="text-sm text-gray-500">
                        Showing <strong>{{ itemCount }}</strong> of <strong>{{ donations?.total ?? 0 }}</strong>
                    </span>
                </div>

                <div>
                    <Link href="/manage/donations/create">
                        <FilledButton variant="primary">
                            <SvgIcon :svg="Icons.plus" />
                            Create Donation
                        </FilledButton>
                    </Link>
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
</template>
