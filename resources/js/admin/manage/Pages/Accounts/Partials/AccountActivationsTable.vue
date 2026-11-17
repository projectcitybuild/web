<script setup lang="ts">
import { AccountActivation } from '../../../Data/AccountActivation'
import { format } from '../../../Utilities/DateFormatter'
import DataTable from '../../../Components/DataTable.vue'

interface Props {
    activations: AccountActivation[],
}
defineProps<Props>()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'created_at', label: 'Created At' },
    { key: 'expires_at', label: 'Expires At' },
]
</script>

<template>
    <DataTable :fields="fields" :rows="activations" empty-message="No pending activations">
        <template #created_at="{ item }">
            {{ format(item.created_at) }}
        </template>
        <template #expires_at="{ item }">
            {{ format(item.expires_at) }}
        </template>
    </DataTable>
</template>
