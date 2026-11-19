<script setup lang="ts">
import { Routes } from '../../../../../Navigation/Routes'
import LinkButton from '../../../../../Components/LinkButton.vue'
import SvgIcon from '../../../../../Components/SvgIcon.vue'
import { Icons } from '../../../../../Navigation/Icons'
import { computed } from 'vue'
import DataTable from '../../../../../Components/DataTable.vue'
import type { SortableTableProps } from '../../../../../Composables/useTableSorting'
import usePermissions from '../../../../../Composables/usePermissions'
import { format } from '../../../../../Utilities/DateFormatter'
import Pill from '../../../../../Components/Pill.vue'

interface Props extends SortableTableProps {
    perks: App.Http.Data.AdminDonationPerkData[],
}
const props = defineProps<Props>()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'account_username', label: 'Account', sortKey: 'account_id' },
    { key: 'donation_id', label: 'Donation' },
    { key: 'donation_tier_name', label: 'Tier', sortKey: 'donation_tier_id' },
    { key: 'status', label: 'Status', sortKey: 'is_active' },
    { key: 'expires', label: 'Expires', sortKey: 'expires_at' },
]

const rows = computed(() => props.perks.map((perk) => ({
    ...perk,
    status: perk.is_active && (perk.expires_at === null || new Date(perk.expires_at) > new Date())
        ? 'Active'
        : 'Inactive',
    expires: perk.expires_at ? format(perk.expires_at) : 'Never',
})))

const { can } = usePermissions()
</script>

<template>
    <DataTable :fields="fields" :rows="rows" :show-index="true" :sort="props.sort" :direction="props.direction" :on-sort="props.onSort">
        <template #account_username="{ item }">
            {{ item.account_username || `Account #${item.account_id}` }}
        </template>

        <template #donation_tier_name="{ item }">
            {{ item.donation_tier_name || '-' }}
        </template>

        <template #status="{ item }">
            <Pill :variant="item.status === 'Active' ? 'success' : 'danger'">
                {{ item.status }}
            </Pill>
        </template>

        <template #actions="{ item }">
            <LinkButton
                v-if="can('web.manage.donations.edit')"
                :href="Routes.manage.donationPerks.edit(item.id)"
                variant="outline"
                size="sm"
            >
                <SvgIcon :svg="Icons.pencil" aria-hidden="true" />
                Edit
            </LinkButton>
        </template>
    </DataTable>
</template>
