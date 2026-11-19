<script setup lang="ts">
import { Routes } from '../../../../../Navigation/Routes'
import SvgIcon from '../../../../../Components/SvgIcon.vue'
import { Icons } from '../../../../../Navigation/Icons'
import DataTable from '../../../../../Components/DataTable.vue'
import usePermissions from '../../../../../Composables/usePermissions'
import LinkButton from '../../../../../Components/LinkButton.vue'
import type { SortableTableProps } from '../../../../../Composables/useTableSorting'

interface Props extends SortableTableProps {
    permissions: App.Http.Data.AdminPermissionData[],
}
const props = defineProps<Props>()

const { can } = usePermissions()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'name', label: 'Name' },
]
</script>

<template>
    <DataTable :fields="fields" :rows="props.permissions" :sort="props.sort" :direction="props.direction" :on-sort="props.onSort">
        <template #actions="{ item }" v-if="can('web.manage.permissions.edit')">
            <LinkButton
                variant="outline"
                size="md"
                :href="Routes.manage.permissions.edit(item.id)"
            >
                <SvgIcon :svg="Icons.pencil" aria-hidden="true" />
                Edit
            </LinkButton>
        </template>
    </DataTable>
</template>
