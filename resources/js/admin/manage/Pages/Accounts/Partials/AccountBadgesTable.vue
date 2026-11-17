<script setup lang="ts">
import type { Badge } from '../../../Data/Badge'
import BooleanCheck from '../../../Components/BooleanCheck.vue'
import DataTable from '../../../Components/DataTable.vue'

interface Props {
    badges: Badge[],
}
defineProps<Props>()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'display_name', label: 'Display Name' },
    { key: 'unicode_icon', label: 'Unicode Icon' },
    { key: 'list_hidden', label: 'Hidden in List?' },
]
</script>

<template>
    <DataTable v-if="badges.length > 0" :fields="fields" :rows="badges">
        <template #list_hidden="{ item }">
            <BooleanCheck :value="item.list_hidden" />
        </template>
    </DataTable>
    <div v-else class="flex p-4">
        <span class="text-sm text-subtle">No badges</span>
    </div>
</template>
