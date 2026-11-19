<script setup lang="ts">
import { Routes } from '../../../../../Navigation/Routes'
import LinkButton from '../../../../../Components/LinkButton.vue'
import { Link } from '@inertiajs/vue3'
import { format } from '../../../../../Utilities/DateFormatter'
import OutlinedButton from '../../../../../Components/OutlinedButton.vue'
import SvgIcon from '../../../../../Components/SvgIcon.vue'
import { Icons } from '../../../../../Navigation/Icons'
import { computed } from 'vue'
import DataTable from '../../../../../Components/DataTable.vue'
import type { SortableTableProps } from '../../../../../Composables/useTableSorting'

interface Props extends SortableTableProps {
    donations: App.Http.Data.AdminDonationData[],
}
const props = defineProps<Props>()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'account', label: 'Account', sortable: false },
    { key: 'original_amount', label: 'Original Currency', sortable: false },
    { key: 'paid_amount', label: 'Paid Currency', sortable: false },
    { key: 'payment.unit_quantity', label: 'Unit Quantity', sortable: false },
    { key: 'amount', label: 'Legacy Amount' },
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
    <DataTable :fields="fields" :rows="rows" :show-index="true" :sort="props.sort" :direction="props.direction" :on-sort="props.onSort">
        <template #account="{ item }">
            <Link
                :href="Routes.manage.accounts.show(item.account?.id)"
                class="text-primary"
            >
                {{ item.account?.username }}
            </Link>
        </template>

        <template #original_amount="{ item }">
            {{ item.payment?.original_unit_amount }} {{ item.payment?.original_currency }}
        </template>

        <template #paid_amount="{ item }">
            {{ item.payment?.paid_unit_amount }} {{ item.payment?.paid_currency }}
        </template>

        <template #actions="{ item }">
            <LinkButton :href="Routes.manage.donations.edit(item.id)" variant="outline" size="sm">
                <SvgIcon :svg="Icons.pencil" />
                Edit
            </LinkButton>
        </template>
    </DataTable>
</template>
