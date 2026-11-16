<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Role } from '../../../Data/Role'
import BooleanCheck from '../../../Components/BooleanCheck.vue'
import SvgIcon from '../../../Components/SvgIcon.vue'
import { Icons } from '../../../Icons'
import DataTable from '../../../Components/DataTable.vue'
import usePermissions from '../../../Composables/usePermissions'
import LinkButton from '../../../../shared/Components/LinkButton.vue'

interface Props {
    roles: Role[],
}
defineProps<Props>()

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
    { key: 'accounts_count', label: 'Accounts' },
    { key: 'permissions_count', label: 'Permissions' },
]
</script>

<template>
    <DataTable :fields="fields" :rows="roles">
        <template #accounts_count="{ item }">
            <Link
                :href="'/manage/roles/' + item.id + '/accounts'"
                class="text-primary underline"
            >
                {{ item.accounts_count }}
            </Link>
        </template>

        <template #permissions_count="{ item }">
            <a
                :href="'/manage/roles/' + item.id + '/permissions'"
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
                :href="'/manage/roles/' + item.id + '/edit'"
            >
                <SvgIcon :svg="Icons.pencil" aria-hidden="true" />
                Edit
            </LinkButton>
        </template>
    </DataTable>
</template>
