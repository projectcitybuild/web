<script setup lang="ts">
import { Routes } from '../../../../../Navigation/Routes'
import { Link } from '@inertiajs/vue3'
import { format } from '../../../../../Utilities/DateFormatter'
import BooleanCheck from '../../../../../Components/BooleanCheck.vue'
import SvgIcon from '../../../../../Components/SvgIcon.vue'
import { Icons } from '../../../../../Navigation/Icons'
import { computed } from 'vue'
import DataTable from '../../../../../Components/DataTable.vue'
import usePermissions from '../../../../../Composables/usePermissions'
import LinkButton from '../../../../../Components/LinkButton.vue'
import type { SortableTableProps } from '../../../../../Composables/useTableSorting'

interface Props extends SortableTableProps {
    badges: App.Http.Data.AdminAccountBadgeData[],
}
const props = defineProps<Props>()

const { can } = usePermissions()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'display_name', label: 'Display Name' },
    { key: 'unicode_icon', label: 'Unicode Icon' },
    { key: 'accounts_count', label: 'Accounts', sortable: false },
    { key: 'list_hidden', label: 'Hidden in List' },
    { key: 'created_at', label: 'Created At' },
    { key: 'updated_at', label: 'Updated At' },
]
const rows = computed(
    () => props.badges.map((badge) => ({
        ...badge,
        created_at: format(badge.created_at),
        updated_at: format(badge.updated_at),
    }))
)
</script>

<template>
    <DataTable :fields="fields" :rows="rows" :show-index="true" :sort="props.sort" :direction="props.direction" :on-sort="props.onSort">
        <template #accounts_count="{ item }">
            <Link
                :href="Routes.manage.badges.accounts(item.id)"
                class="text-primary-active underline-offset-2 underline"
            >
                {{ item.accounts_count }}
            </Link>
        </template>

        <template #list_hidden="{ item }">
            <BooleanCheck :value="item.list_hidden" />
        </template>

        <template #actions="{ item }" v-if="can('web.manage.badges.edit')">
            <LinkButton
                :href="Routes.manage.badges.edit(item.id)"
                variant="outline"
                size="sm"
            >
                <SvgIcon :svg="Icons.pencil" class="size-4" aria-hidden="true" />
                Edit
            </LinkButton>
        </template>
    </DataTable>
</template>
