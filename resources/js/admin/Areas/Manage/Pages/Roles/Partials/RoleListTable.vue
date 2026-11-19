<script setup lang="ts">
import { Routes } from '../../../../../Navigation/Routes'
import { Link } from '@inertiajs/vue3'
import BooleanCheck from '../../../../../Components/BooleanCheck.vue'
import SvgIcon from '../../../../../Components/SvgIcon.vue'
import { Icons } from '../../../../../Navigation/Icons'
import DataTable from '../../../../../Components/DataTable.vue'
import usePermissions from '../../../../../Composables/usePermissions'
import LinkButton from '../../../../../Components/LinkButton.vue'
import type { SortableTableProps } from '../../../../../Composables/useTableSorting'

interface Props extends SortableTableProps {
    roles: App.Http.Data.AdminRoleData[],
}
const props = defineProps<Props>()

const { can } = usePermissions()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'name', label: 'Name' },
    { key: 'minecraft_name', label: 'Minecraft Name' },
    { key: 'role_type', label: 'Role Type' },
    { key: 'display_priority', label: 'Display Priority' },
    { key: 'is_default', label: 'Is Default?' },
    { key: 'is_admin', label: 'Is Admin?' },
    { key: 'additional_homes', label: 'Additional Homes' },
    { key: 'accounts_count', label: 'Accounts', sortable: false },
    { key: 'permissions_count', label: 'Permissions', sortable: false },
]
</script>

<template>
    <DataTable :fields="fields" :rows="props.roles" :sort="props.sort" :direction="props.direction" :on-sort="props.onSort">
        <template #accounts_count="{ item }">
            <Link
                :href="Routes.manage.roles.accounts(item.id)"
                class="text-primary underline"
            >
                {{ item.accounts_count }}
            </Link>
        </template>

        <template #permissions_count="{ item }">
            <a
                :href="Routes.manage.roles.permissions(item.id)"
                v-if="can('web.manage.permissions.assign')"
                class="text-primary underline"
            >
                {{ item.permissions_count }}
            </a>
            <span v-else>{{ item.permissions_count }}</span>
        </template>

        <template #is_default="{ item }">
            <BooleanCheck :value="item.is_default" />
        </template>

        <template #is_admin="{ item }">
            <BooleanCheck :value="item.is_admin" />
        </template>

        <template #actions="{ item }" v-if="can('web.manage.roles.edit')">
            <LinkButton
                variant="outline"
                size="sm"
                :href="Routes.manage.roles.edit(item.id)"
            >
                <SvgIcon :svg="Icons.pencil" aria-hidden="true" />
                Edit
            </LinkButton>
        </template>
    </DataTable>
</template>
