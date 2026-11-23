<script setup lang="ts">
import { Routes } from '../../../../../Navigation/Routes'
import LinkButton from '../../../../../Components/LinkButton.vue'
import { Link } from '@inertiajs/vue3'
import { format as formatDate } from 'date-fns'
import OutlinedButton from '../../../../../Components/OutlinedButton.vue'
import SvgIcon from '../../../../../Components/SvgIcon.vue'
import { Icons } from '../../../../../Components/SvgIcon.vue'
import { computed } from 'vue'
import DataTable from '../../../../../Components/DataTable.vue'
import type { SortableTableProps } from '../../../../../Composables/useTableSorting'

interface Props extends SortableTableProps {
    donations: App.Http.Data.AdminDonationData[],
}
const props = defineProps<Props>()

const formatCurrency = (amount?: number, currency?: string): string => {
    if (amount === undefined || !currency) {
        return 'Not available'
    }

    const formatter = new Intl.NumberFormat(undefined, {
        style: 'currency',
        currency,
        currencyDisplay: 'code',
    })
    const fractionDigits = formatter.resolvedOptions().maximumFractionDigits ?? 2

    return formatter.format(amount / (10 ** fractionDigits))
}

const formatDonationDate = (value?: string | null): string | null => {
    if (!value) {
        return null
    }

    return formatDate(new Date(value), 'MMM d, yyyy, h:mm a')
}

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'account', label: 'Account', sortable: false },
    { key: 'original_amount', label: 'Original / Unit', sortable: false },
    { key: 'paid_amount', label: 'Paid / Unit', sortable: false },
    { key: 'payment.unit_quantity', label: 'Unit Quantity', sortable: false },
    { key: 'total_paid_amount', label: 'Total Paid', sortable: false },
    { key: 'amount', label: 'Legacy Amount' },
    { key: 'created_at', label: 'Date' },
]
const rows = computed(
    () => props.donations.map((donation) => ({
        ...donation,
        amount: new Intl.NumberFormat().format(donation.amount),
        created_at_display: formatDonationDate(donation.created_at),
        total_paid_amount: donation.payment
            ? donation.payment.paid_unit_amount * donation.payment.unit_quantity
            : undefined,
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
            {{ formatCurrency(item.payment?.original_unit_amount, item.payment?.original_currency) }}
        </template>

        <template #paid_amount="{ item }">
            {{ formatCurrency(item.payment?.paid_unit_amount, item.payment?.paid_currency) }}
        </template>

        <template #total_paid_amount="{ item }">
            {{ formatCurrency(item.total_paid_amount, item.payment?.paid_currency) }}
        </template>

        <template #created_at="{ item }">
            <time :datetime="item.created_at ?? undefined">
                {{ item.created_at_display }}
            </time>
        </template>

        <template #actions="{ item }">
            <LinkButton :href="Routes.manage.donations.edit(item.id)" variant="outline" size="sm">
                <SvgIcon :svg="Icons.pencil" />
                Edit
            </LinkButton>
        </template>
    </DataTable>
</template>
