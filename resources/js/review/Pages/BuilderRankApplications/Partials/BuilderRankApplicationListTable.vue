<script setup lang="ts">
import { distance, distanceFromNow, format } from '../../../../manage/Utilities/DateFormatter'
import { computed } from 'vue'
import DataTable from '../../../../manage/Components/DataTable.vue'
import { BanAppealStatus } from '../../../../manage/Data/BanAppealStatus'
import FilledButton from '../../../../manage/Components/FilledButton.vue'
import Pill from '../../../../manage/Components/Pill.vue'
import { Link } from '@inertiajs/vue3'
import { BuilderRankApplication, BuilderRankApplicationStatus } from '../../../../manage/Data/BuilderRankApplication'

interface Props {
    applications: BuilderRankApplication[],
}
const props = defineProps<Props>()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'status', label: 'Status' },
    { key: 'waiting_time', label: 'Waiting Time' },
    { key: 'username', label: 'Account' },
    { key: 'created_at', label: 'Created At' },
    { key: 'decided_at', label: 'Reviewed At' },
]
const rows = computed(
    () => props.applications.map((application) => ({
        ...application,
        waiting_time: application.closed_at == null
            ? distanceFromNow(application.created_at)
            : distance(application.created_at, application.closed_at),
        created_at: format(application.created_at),
        updated_at: format(application.updated_at),
        closed_at: format(application.closed_at) ?? '-',
    }))
)
</script>

<template>
    <DataTable :fields="fields" :rows="rows" :show-index="true" class="border-t border-gray-200">
        <template #status="{ item }">
            <Pill v-if="item.status === BuilderRankApplicationStatus.pending" variant="danger">
                Awaiting Review
            </Pill>
            <Pill v-if="item.status === BuilderRankApplicationStatus.approved" variant="secondary">
                Unbanned
            </Pill>
            <Pill v-if="item.status === BuilderRankApplicationStatus.denied" variant="secondary">
                Denied
            </Pill>
        </template>

        <template #username="{ item }">
            <a
                :href="'/manage/accounts/' + item.account_id"
                class="text-blue-500"
            >
                {{ item.account.username }}
            </a>
        </template>

        <template #actions="{ item }">
            <Link :href="'/review/ban-appeals/' + item.id">
                <FilledButton v-if="item.status === BanAppealStatus.pending" variant="danger">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                        <path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" />
                        <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd" />
                    </svg>
                    Review
                </FilledButton>
                <FilledButton v-else variant="secondary">
                    View
                </FilledButton>
            </Link>
        </template>
    </DataTable>
</template>
