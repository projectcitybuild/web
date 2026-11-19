<script setup lang="ts">
import { Routes } from '../../../../Routes'
import InfinitePagination from '../../../Components/InfinitePagination.vue'
import { format } from '../../../Utilities/DateFormatter'
import OutlinedButton from '../../../Components/OutlinedButton.vue'
import { BanAppealStatus } from '../../../../../generated/openapi-enums'
import Pill from '../../../Components/Pill.vue'
import DataTable from '../../../Components/DataTable.vue'

interface Props {
    account_id: number,
}
defineProps<Props>()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'status', label: 'Status' },
    { key: 'created_at', label: 'Created At' },
]
</script>

<template>
    <!-- @vue-generic {App.Http.Data.AdminBanAppealData} -->
    <InfinitePagination
        :path="'/manage/accounts/' + account_id + '/ban-appeals'"
        v-slot="source"
        class="overflow-x-auto"
    >
        <DataTable :fields="fields" :rows="source.data">
            <template #status="{ item }">
                <Pill v-if="item.status === BanAppealStatus.pending">Pending</Pill>
                <Pill v-if="item.status === BanAppealStatus.denied" variant="danger">Denied</Pill>
                <Pill v-if="item.status === BanAppealStatus.acceptedUnban" variant="success">Unbanned</Pill>
            </template>
            <template #created_at="{ item }">
                {{ format(item.created_at) }}
            </template>
            <template #actions="{ item }">
                <a :href="Routes.review.banAppeals.show(item.id)">
                    <OutlinedButton variant="secondary" size="sm">View</OutlinedButton>
                </a>
            </template>
        </DataTable>
    </InfinitePagination>
</template>
