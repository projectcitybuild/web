<script setup lang="ts">
import { format } from '../../../Utilities/DateFormatter'
import { PlayerIP } from '../../../Data/PlayerIP'
import DataTable from '../../../Components/DataTable.vue'

interface Props {
    ips: PlayerIP[],
}
defineProps<Props>()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'ip', label: 'IP Address' },
    { key: 'times_connected', label: 'Times Connected' },
    { key: 'updated_at', label: 'Last Used' },
    { key: 'created_at', label: 'First Used' },
]
</script>

<template>
    <DataTable :fields="fields" :rows="ips">
        <template #updated_at="{ item }">
            {{ format(item.updated_at) }}
        </template>
        <template #created_at="{ item }">
            {{ format(item.created_at) }}
        </template>
    </DataTable>
</template>
