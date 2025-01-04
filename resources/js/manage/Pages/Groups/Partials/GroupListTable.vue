<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Group } from '../../../Data/Group'
import BooleanCheck from '../../../Components/BooleanCheck.vue'
import FilledButton from '../../../Components/FilledButton.vue'
import DataTable from '../../../Components/DataTable.vue'
import SvgIcon from '../../../Components/SvgIcon.vue'

interface Props {
    groups: Group[],
}
defineProps<Props>()

const fields = [
    { key: 'group_id', label: 'ID' },
    { key: 'name', label: 'Name' },
    { key: 'accounts_count', label: 'Accounts' },
    { key: 'minecraft_name', label: 'Minecraft Name' },
    { key: 'group_type', label: 'Group Type' },
    { key: 'display_priority', label: 'Display Priority' },
    { key: 'is_default', label: 'Is Default?' },
    { key: 'is_admin', label: 'Is Admin?' },
]
</script>

<template>
    <DataTable :fields="fields" :rows="groups">
        <template #accounts_count="{ item }">
            <Link
                :href="'/manage/groups/' + item.group_id + '/accounts'"
                class="text-blue-500 font-bold"
            >
                {{ item.accounts_count }}
            </Link>
        </template>
        <template #is_default="{ item }">
            <BooleanCheck :value="item.is_default" />
        </template>
        <template #is_admin="{ item }">
            <BooleanCheck :value="item.is_default" />
        </template>
        <template #actions="{ item }">
            <Link :href="'/manage/groups/' + item.group_id + '/edit'">
                <FilledButton variant="secondary">
                    Edit
                </FilledButton>
            </Link>
        </template>
    </DataTable>
</template>
