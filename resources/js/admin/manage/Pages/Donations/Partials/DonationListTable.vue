<script setup lang="ts">
import LinkButton from '../../../../shared/Components/LinkButton.vue'
import { Link } from '@inertiajs/vue3'
import { Donation } from '../../../Data/Donation'
import { format } from '../../../Utilities/DateFormatter'
import OutlinedButton from '../../../Components/OutlinedButton.vue'
import SvgIcon from '../../../Components/SvgIcon.vue'
import { Icons } from '../../../Icons'
import { computed } from 'vue'
import DataTable from '../../../Components/DataTable.vue'

interface Props {
    donations: Donation[],
}
const props = defineProps<Props>()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'account', label: 'Account' },
    { key: 'original_amount', label: 'Original Currency' },
    { key: 'paid_amount', label: 'Paid Currency' },
    { key: 'payment.unit_quantity', label: 'Unit Quantity' },
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
    <DataTable :fields="fields" :rows="rows" :show-index="true">
        <template #account="{ item }">
            <Link
                :href="'/manage/accounts/' + item.account?.id"
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
            <LinkButton :href="'/manage/donations/' + item.id + '/edit'" variant="outline" size="sm">
                <SvgIcon :svg="Icons.pencil" />
                Edit
            </LinkButton>
        </template>
    </DataTable>
</template>
