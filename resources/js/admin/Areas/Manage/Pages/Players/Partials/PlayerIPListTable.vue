<script setup lang="ts">
import { format } from '../../../../../Utilities/DateFormatter'
import DataTable from '../../../../../Components/DataTable.vue'

interface Props {
    ips: App.Http.Data.MinecraftPlayerIpData[],
}
defineProps<Props>()

const fields = [
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
