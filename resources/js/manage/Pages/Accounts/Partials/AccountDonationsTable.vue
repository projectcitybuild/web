<script setup lang="ts">
import type { Donation } from '../../../Data/Donation'
import { format } from '../../../Utilities/DateFormatter'
import { Link } from '@inertiajs/vue3'
import FilledButton from '../../../Components/FilledButton.vue'
import OutlinedButton from '../../../Components/OutlinedButton.vue'
import DataTable from '../../../Components/DataTable.vue'

interface Props {
    donations: Donation[],
}
defineProps<Props>()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'amount', label: 'Amount' },
    { key: 'created_at', label: 'Date' },
]
</script>

<template>
    <DataTable v-if="donations.length > 0" :fields="fields" :rows="donations">
        <template #created_at="{ item }">
            {{ format(item.created_at) }}
        </template>
        <template #actions="{ item }">
                <Link :href="'/manage/donations/' + item.id + '/edit'">
                    <OutlinedButton variant="secondary" size="sm">
                        Edit
                    </OutlinedButton>
                </Link>
        </template>
    </DataTable>
    <div v-else class="flex p-4">
        <span class="text-sm text-gray-400">No donations</span>
    </div>
</template>
