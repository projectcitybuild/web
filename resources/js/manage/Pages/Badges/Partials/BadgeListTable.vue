<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Badge } from '../../../Data/Badge'
import { format } from '../../../Utilities/DateFormatter'
import BooleanCheck from '../../../Components/BooleanCheck.vue'
import FilledButton from '../../../Components/FilledButton.vue'
import { computed } from 'vue'
import DataTable from '../../../Components/DataTable.vue'

interface Props {
    badges: Badge[],
}
const props = defineProps<Props>()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'display_name', label: 'Display Name' },
    { key: 'unicode_icon', label: 'Unicode Icon' },
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
    <DataTable :fields="fields" :rows="rows" :show-index="true" class="border-t border-gray-200">
        <template #list_hidden="{ item }">
            <BooleanCheck :value="item.list_hidden" />
        </template>

        <template #actions="{ item }">
            <Link :href="'/manage/badges/' + item.id + '/edit'">
                <FilledButton variant="secondary">
                    Edit
                </FilledButton>
            </Link>
        </template>
    </DataTable>
</template>
