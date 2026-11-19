<script setup lang="ts">
import { Routes } from '../../../../../Navigation/Routes'
import SvgIcon from '../../../../../Components/SvgIcon.vue'
import { Icons } from '../../../../../Navigation/Icons'
import DataTable from '../../../../../Components/DataTable.vue'
import usePermissions from '../../../../../Composables/usePermissions';
import LinkButton from '../../../../../Components/LinkButton.vue'
import type { SortableTableProps } from '../../../../../Composables/useTableSorting'

interface Props extends SortableTableProps {
    servers: App.Http.Data.AdminServerData[],
}
const props = defineProps<Props>()

const { can } = usePermissions()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'name', label: 'Name' },
    { key: 'ip', label: 'IP Address' },
    { key: 'port', label: 'Port' },
    { key: 'web_port', label: 'Web Port' },
]
</script>

<template>
    <DataTable :fields="fields" :rows="props.servers" :sort="props.sort" :direction="props.direction" :on-sort="props.onSort">
        <template #actions="{ item }" v-if="can('web.manage.servers.edit')">
            <LinkButton
                :href="Routes.manage.servers.edit(item.id)"
                variant="outline"
                size="sm"
            >
                <SvgIcon :svg="Icons.pencil" aria-hidden="true" />
                Edit
            </LinkButton>
        </template>
    </DataTable>
</template>
