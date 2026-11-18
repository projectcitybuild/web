<script setup lang="ts">
import LinkButton from '../../../../shared/Components/LinkButton.vue'
import { format } from '../../../Utilities/DateFormatter'
import { Link } from '@inertiajs/vue3'
import FilledButton from '../../../Components/FilledButton.vue'
import OutlinedButton from '../../../Components/OutlinedButton.vue'
import DataTable from '../../../Components/DataTable.vue'

interface Props {
    donations: App.Http.Data.AdminDonationData[],
}
defineProps<Props>()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'amount', label: 'Amount' },
    { key: 'created_at', label: 'Date' },
]
</script>

<template>
    <DataTable :fields="fields" :rows="donations" empty-message="No donations">
        <template #created_at="{ item }">
            {{ format(item.created_at) }}
        </template>
        <template #actions="{ item }">
                <LinkButton :href="'/manage/donations/' + item.id + '/edit'" variant="outline" size="sm">
                    Edit
                </LinkButton>
        </template>
    </DataTable>
</template>
