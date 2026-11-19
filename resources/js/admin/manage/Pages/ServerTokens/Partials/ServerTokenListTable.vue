<script setup lang="ts">
import { Routes } from '../../../../Routes'
import LinkButton from '../../../../shared/Components/LinkButton.vue'
import { Link } from '@inertiajs/vue3'
import { format } from '../../../Utilities/DateFormatter'
import OutlinedButton from '../../../Components/OutlinedButton.vue'
import SvgIcon from '../../../Components/SvgIcon.vue'
import { Icons } from '../../../Icons'
import { computed } from 'vue'
import DataTable from '../../../Components/DataTable.vue'
import usePermissions from '../../../Composables/usePermissions'

interface Props {
    tokens: App.Http.Data.AdminServerTokenData[],
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
    <DataTable :fields="fields" :rows="rows">
        <template #allowed_ips="{ item }">
            <span v-if="!item.allowed_ips">*</span>
            <ul v-else>
                <li v-for="ip in item.allowed_ips.split(',')">{{ ip }}</li>
            </ul>
        </template>

        <template #actions="{ item }" v-if="can('web.manage.server_tokens.edit')">
            <LinkButton :href="Routes.manage.serverTokens.edit(item.id)" variant="outline" size="sm">
                <SvgIcon :svg="Icons.pencil" />
                Edit
            </LinkButton>
        </template>
    </DataTable>
</template>
