<script setup lang="ts">
import DataTable from '../../../../../Components/DataTable.vue'
import { format, timeAgo } from '../../../../../Utilities/DateFormatter'
import LinkButton from '../../../../../Components/LinkButton.vue'
import SvgIcon from '../../../../../Components/SvgIcon.vue'
import { Icons } from '../../../../../Components/SvgIcon.vue'

interface Props {
    audits: App.Http.Data.AdminAuditListEntryData[]
}

defineProps<Props>()

const fields = [
    { key: 'actor', label: 'Actor' },
    { key: 'action', label: 'Action' },
    { key: 'event', label: 'Event' },
    { key: 'subject', label: 'Subject' },
    { key: 'createdAt', label: 'Time' },
]
</script>

<template>
    <DataTable :fields="fields" :rows="audits">
        <template #createdAt="{ item }">
            <div>
                <div class="text-sm font-medium">{{ format(item.createdAt) }}</div>
                <div class="text-xs text-muted">{{ timeAgo(item.createdAt) }}</div>
            </div>
        </template>

        <template #actions="{ item }">
            <LinkButton :href="item.showUrl" variant="secondary" size="sm">
                <SvgIcon :svg="Icons.chevronRight" class="size-4" aria-hidden="true" />
            </LinkButton>
        </template>
    </DataTable>
</template>
