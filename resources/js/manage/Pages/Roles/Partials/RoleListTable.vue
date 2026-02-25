<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Role } from '../../../Data/Role'
import BooleanCheck from '../../../Components/BooleanCheck.vue'
import FilledButton from '../../../Components/FilledButton.vue'
import DataTable from '../../../Components/DataTable.vue'
import usePermissions from '../../../Composables/usePermissions'

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
                class="text-blue-500 underline"
            >
                {{ item.accounts_count }}
            </Link>
        </template>

        <template #permissions_count="{ item }">
            <a
                :href="'/manage/roles/' + item.id + '/permissions'"
                v-if="can('web.manage.permissions.assign')"
                class="text-blue-500 underline"
            >
                {{ item.permissions_count }}
            </a>
            <span v-else>{{ item.permissions_count }}</span>
        </template>

        <template #is_default="{ item }">
            <BooleanCheck :value="item.is_default" />
        </template>

        <template #is_admin="{ item }">
            <BooleanCheck :value="item.is_default" />
        </template>

        <template #actions="{ item }" v-if="can('web.manage.roles.edit')">
            <Link :href="'/manage/roles/' + item.id + '/edit'">
                <FilledButton variant="secondary">
                    Edit
                </FilledButton>
            </Link>
        </template>
    </DataTable>
</template>
