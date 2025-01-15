<script setup lang="ts">
import { Deferred, Head, Link } from '@inertiajs/vue3'
import { Paginated } from '../../Data/Paginated'
import type { Badge } from '../../Data/Badge'
import BadgeListTable from './Partials/BadgeListTable.vue'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import Card from '../../Components/Card.vue'
import FilledButton from '../../Components/FilledButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import SpinnerRow from '../../Components/SpinnerRow.vue'

interface Props {
    success?: string,
    badges?: Paginated<Badge>,
}

defineProps<Props>()
</script>

<template>
    <Head title="Manage Badges"/>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <Card>
            <div
                class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                <div class="w-full md:w-1/2">

                </div>
                <div>
                    <Link href="/manage/badges/create">
                        <FilledButton variant="primary">
                            <SvgIcon icon="plus" />
                            Create Badge
                        </FilledButton>
                    </Link>
                </div>
            </div>

            <Deferred data="badges">
                <template #fallback>
                    <SpinnerRow />
                </template>

                <InfinitePagination
                    :initial="badges"
                    v-slot="source"
                >
                    <BadgeListTable :badges="source.data"/>
                </InfinitePagination>
            </Deferred>
        </Card>
    </section>
</template>
