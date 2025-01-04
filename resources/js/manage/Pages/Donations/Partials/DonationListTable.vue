<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Donation } from '../../../Data/Donation'
import { format } from '../../../Utilities/DateFormatter'
import FilledButton from '../../../Components/FilledButton.vue'
import { computed } from 'vue'
import DataTable from '../../../Components/DataTable.vue'

interface Props {
    donations: Donation[],
}
const props = defineProps<Props>()

const fields = [
    { key: 'donation_id', label: 'ID' },
    { key: 'account', label: 'Skin' },
    { key: 'amount', label: 'Amount' },
    { key: 'created_at', label: 'Date' },
]
const rows = computed(
    () => props.donations.map((donation) => ({
        ...donation,
        amount: donation.amount,
        created_at: format(donation.created_at),
        updated_at: format(donation.updated_at),
    }))
)
</script>

<template>
    <DataTable :fields="fields" :rows="rows" :show-index="true" class="border-t border-gray-200">
        <template #account="{ item }">
            <Link
                :href="'/manage/accounts/' + item.account?.account_id"
                class="text-blue-500"
            >
                {{ item.account?.username }}
            </Link>
        </template>

        <template #actions="{ item }">
            <Link :href="'/manage/donations/' + item.donation_id + '/edit'">
                <FilledButton variant="secondary">
                    Edit
                </FilledButton>
            </Link>
        </template>
    </DataTable>
</template>
