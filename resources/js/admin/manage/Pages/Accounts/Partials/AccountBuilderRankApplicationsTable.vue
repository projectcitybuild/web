<script setup lang="ts">
import { Routes } from '../../../../Routes'
import InfinitePagination from '../../../Components/InfinitePagination.vue'
import { format } from '../../../Utilities/DateFormatter'
import OutlinedButton from '../../../Components/OutlinedButton.vue'
import { BuilderRankApplicationStatus } from '../../../Data/BuilderRankApplication'
import Pill from '../../../Components/Pill.vue'
import DataTable from '../../../Components/DataTable.vue'

interface Props {
    account_id: number,
}
defineProps<Props>()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'status', label: 'Status' },
    { key: 'current_builder_rank', label: 'Current Build Rank' },
    { key: 'created_at', label: 'Created At' },
]
</script>

<template>
    <!-- @vue-generic {App.Http.Data.AdminBuilderRankApplicationData} -->
    <InfinitePagination
        :path="'/manage/accounts/' + account_id + '/builder-ranks'"
        v-slot="source"
        class="overflow-x-auto"
    >
        <DataTable :fields="fields" :rows="source.data">
            <template #status="{ item }">
                <Pill v-if="item.status === BuilderRankApplicationStatus.pending">Pending</Pill>
                <Pill v-if="item.status === BuilderRankApplicationStatus.denied" variant="danger">Denied</Pill>
                <Pill v-if="item.status === BuilderRankApplicationStatus.approved" variant="success">Approved</Pill>
            </template>
            <template #created_at="{ item }">
                {{ format(item.created_at) }}
            </template>
            <template #actions="{ item }">
                <a :href="Routes.review.builderRanks.show(item.id)">
                    <OutlinedButton variant="secondary" size="sm">View</OutlinedButton>
                </a>
            </template>
        </DataTable>
    </InfinitePagination>
</template>
