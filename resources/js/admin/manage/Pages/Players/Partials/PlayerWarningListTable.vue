<script setup lang="ts">
import LinkButton from '../../../../shared/Components/LinkButton.vue'
import { format } from '../../../Utilities/DateFormatter'
import { Link } from '@inertiajs/vue3'
import OutlinedButton from '../../../Components/OutlinedButton.vue'
import SvgIcon from '../../../Components/SvgIcon.vue'
import { Icons } from '../../../Icons'
import { PlayerWarning } from '../../../Data/PlayerWarning';
import DataTable from '../../../Components/DataTable.vue'

interface Props {
    warnings: PlayerWarning[],
}
defineProps<Props>()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'reason', label: 'Reason' },
    { key: 'created_at', label: 'Created At' },
]
</script>

<template>
    <DataTable :fields="fields" :rows="warnings">
        <template #created_at="{ item }">
            {{ format(item.created_at) }}
        </template>
        <template #actions="{ item }">
            <LinkButton :href="'/manage/warnings/' + item.id + '/edit'" variant="outline" size="sm">
                <SvgIcon :svg="Icons.pencil" />
                Edit
            </LinkButton>
        </template>
    </DataTable>
</template>
