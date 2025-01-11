<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { Paginated } from '../../../manage/Data/Paginated'
import InfinitePagination from '../../../manage/Components/InfinitePagination.vue'
import SuccessAlert from '../../../manage/Components/SuccessAlert.vue'
import Card from '../../../manage/Components/Card.vue'
import type { BuilderRankApplication } from '../../../manage/Data/BuilderRankApplication'
import BuilderRankApplicationListTable from './Partials/BuilderRankApplicationListTable.vue'

interface Props {
    success?: string,
    applications: Paginated<BuilderRankApplication>,
}
defineProps<Props>()
</script>

<template>
    <Head title="Builder Rank Applications" />

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <Card>
            <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
            </div>

            <InfinitePagination
                :initial="applications"
                v-slot="source"
            >
                <BuilderRankApplicationListTable :applications="source.data"/>
            </InfinitePagination>
        </Card>
    </section>
</template>
