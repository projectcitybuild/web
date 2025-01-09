<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import type { ServerToken } from '../../../Data/ServerToken'
import { format } from '../../../Utilities/DateFormatter'
import FilledButton from '../../../Components/FilledButton.vue'
import { computed } from 'vue'
import DataTable from '../../../Components/DataTable.vue'

interface Props {
    tokens: ServerToken[],
}
const props = defineProps<Props>()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'token', label: 'Token' },
    { key: 'description', label: 'Description' },
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
        <template #actions="{ item }">
            <Link :href="'/manage/server-tokens/' + item.id + '/edit'">
                <FilledButton variant="secondary">
                    Edit
                </FilledButton>
            </Link>
        </template>
    </DataTable>
</template>
