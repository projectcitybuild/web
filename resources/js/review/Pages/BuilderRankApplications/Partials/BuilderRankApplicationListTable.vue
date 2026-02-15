<script setup lang="ts">
import { distance, distanceFromNow, format } from '../../../../manage/Utilities/DateFormatter'
import { computed } from 'vue'
import DataTable from '../../../../manage/Components/DataTable.vue'
import FilledButton from '../../../../manage/Components/FilledButton.vue'
import Pill from '../../../../manage/Components/Pill.vue'
import { Link } from '@inertiajs/vue3'
import { BuilderRankApplication, BuilderRankApplicationStatus } from '../../../../manage/Data/BuilderRankApplication'
import SvgIcon from '../../../../manage/Components/SvgIcon.vue'
import { Icons } from '../../../../manage/Icons'

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
                Approved
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
            <Link :href="'/review/builder-ranks/' + item.id">
                <FilledButton v-if="item.status === BuilderRankApplicationStatus.pending" variant="danger">
                    <SvgIcon :svg="Icons.eye" />
                    Review
                </FilledButton>
                <FilledButton v-else variant="secondary">
                    View
                </FilledButton>
            </Link>
        </template>
    </DataTable>
</template>
