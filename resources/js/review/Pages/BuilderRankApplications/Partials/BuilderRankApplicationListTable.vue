<script setup lang="ts">
import { distance, distanceFromNow, format } from '../../../../manage/Utilities/DateFormatter'
import { computed } from 'vue'
import DataTable from '../../../../manage/Components/DataTable.vue'
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
    <DataTable :fields="fields" :rows="rows" :show-index="true" class="border-t border-border">
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
                class="font-medium text-primary-active underline-offset-2 hover:underline"
            >
                {{ item.account.username }}
            </a>
        </template>

        <template #actions="{ item }">
            <Link
                :href="'/review/builder-ranks/' + item.id"
                class="inline-flex min-h-8 items-center justify-center gap-2 rounded-[var(--radius-md)] px-3 text-xs font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus focus-visible:ring-offset-2 focus-visible:ring-offset-surface motion-reduce:transition-none"
                :class="item.status === BuilderRankApplicationStatus.pending
                    ? 'bg-primary text-primary-foreground hover:bg-primary-hover active:bg-primary-active'
                    : 'bg-surface-muted text-foreground hover:bg-surface-hover active:bg-surface-hover'"
            >
                <SvgIcon v-if="item.status === BuilderRankApplicationStatus.pending" :svg="Icons.eye" aria-hidden="true" />
                {{ item.status === BuilderRankApplicationStatus.pending ? 'Review' : 'View' }}
            </Link>
        </template>
    </DataTable>
</template>
