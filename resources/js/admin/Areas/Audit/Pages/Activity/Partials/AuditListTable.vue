<script setup lang="ts">
import Badge from '../../../../../Components/Badge.vue'
import DataTable from '../../../../../Components/DataTable.vue'
import LinkButton from '../../../../../Components/LinkButton.vue'
import { format, timeAgo } from '../../../../../Utilities/DateFormatter.js'
import type { SortableTableProps } from '../../../../../Composables/useTableSorting'

interface Props extends SortableTableProps {
    audits: App.Http.Data.AdminAuditListEntryData[],
}
const props = defineProps<Props>()

const fields = [
    { key: 'actor', label: 'Actor', sortable: false },
    { key: 'action', label: 'Action', sortable: false },
    { key: 'createdAt', label: 'When', sortKey: 'createdAt' },
    { key: 'subject', label: 'Subject', sortable: false },
]

function eventVariant(event: string): 'default' | 'success' | 'info' | 'warning' | 'danger' {
    switch (event) {
        case 'created':
            return 'success'
        case 'updated':
            return 'info'
        case 'deleted':
            return 'danger'
        case 'restored':
            return 'warning'
        default:
            return 'default'
    }
}
</script>

<template>
    <DataTable :fields="fields" :rows="props.audits" :sort="props.sort" :direction="props.direction" :on-sort="props.onSort" empty-message="No audit logs match these filters.">
        <template #actor="{ item }">
            <span class="font-medium text-foreground">{{ item.actor }}</span>
        </template>

        <template #action="{ item }">
            <div class="flex flex-wrap items-center gap-2">
                <span>{{ item.action }}</span>
                <Badge :variant="eventVariant(item.event)" size="sm">{{ item.event }}</Badge>
            </div>
        </template>

        <template #createdAt="{ item }">
            <time :datetime="item.createdAt" :title="format(item.createdAt) ?? undefined">
                {{ timeAgo(item.createdAt) }}
            </time>
        </template>

        <template #actions="{ item }">
            <LinkButton :href="item.showUrl" variant="outline" size="sm">
                View details
            </LinkButton>
        </template>
    </DataTable>
</template>
