<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import type { ServerToken } from '../../../Data/ServerToken'
import { format } from '../../../Utilities/DateFormatter'
import FilledButton from '../../../Components/FilledButton.vue'
import { computed } from 'vue'
import DataTable from '../../../Components/DataTable.vue'
import usePermissions from '../../../Composables/usePermissions'

interface Props {
    tokens: ServerToken[],
}
const props = defineProps<Props>()

const { can } = usePermissions()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'token', label: 'Token' },
    { key: 'description', label: 'Description' },
    { key: 'allowed_ips', label: 'Allowed IPs' },
    { key: 'created_at', label: 'Created At' },
    { key: 'updated_at', label: 'Updated At' },
]
const rows = computed(
    () => props.tokens.map((token) => ({
        ...token,
        created_at: format(token.created_at),
        updated_at: format(token.updated_at),
    }))
)
</script>

<template>
    <DataTable :fields="fields" :rows="rows" class="border-t border-gray-200">
        <template #allowed_ips="{ item }">
            <span v-if="!item.allowed_ips">*</span>
            <ul v-else>
                <li v-for="ip in item.allowed_ips.split(',')">{{ ip }}</li>
            </ul>
        </template>

        <template #actions="{ item }" v-if="can('web.manage.server_tokens.edit')">
            <Link :href="'/manage/server-tokens/' + item.id + '/edit'">
                <FilledButton variant="secondary">
                    Edit
                </FilledButton>
            </Link>
        </template>
    </DataTable>
</template>
