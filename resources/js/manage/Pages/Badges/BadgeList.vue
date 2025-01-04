<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { Paginated } from '../../Data/Paginated'
import type { Badge } from '../../Data/Badge'
import BadgeListTable from './Partials/BadgeListTable.vue'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import Card from '../../Components/Card.vue'
import FilledButton from '../../Components/FilledButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'

interface Props {
    success?: string,
    badges: Paginated<Badge>,
}

defineProps<Props>()
</script>

<template>
    <Head title="Manage Badges"/>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <div class="mx-auto max-w-screen-xl">
            <Card class="overflow-hidden">
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

                <InfinitePagination
                    :initial="badges"
                    v-slot="source"
                    class="overflow-x-auto"
                >
                    <BadgeListTable :badges="source.data"/>
                </InfinitePagination>
            </Card>
        </div>
    </section>
</template>
