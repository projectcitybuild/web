<script setup lang="ts">
import DataTable from '../../../../../Components/DataTable.vue'
import { format, formatDuration } from '../../../../../Utilities/DateFormatter'

interface Props {
    sessions: App.Http.Data.MinecraftPlayerSessionData[]
}

defineProps<Props>()

const fields = [
    { key: 'starts_at', label: 'Started' },
    { key: 'ends_at', label: 'Ended' },
    { key: 'seconds', label: 'Duration' },
]
</script>

<template>
    <DataTable :fields="fields" :rows="sessions" show-index>
        <template #starts_at="{ item }">
            {{ item.starts_at ? format(item.starts_at) : 'Unknown' }}
        </template>

        <template #ends_at="{ item }">
            {{ format(item.ends_at) }}
        </template>

        <template #seconds="{ item }">
            {{ formatDuration(item.seconds) }}
        </template>
    </DataTable>
</template>
