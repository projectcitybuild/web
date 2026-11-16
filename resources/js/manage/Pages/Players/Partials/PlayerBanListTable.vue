<script setup lang="ts">
import { format } from '../../../Utilities/DateFormatter'
import { Link } from '@inertiajs/vue3'
import { PlayerBan } from '../../../Data/PlayerBan'
import PlayerBanStatus from '../../PlayerBans/Partials/PlayerBanStatus.vue'
import FilledButton from '../../../Components/FilledButton.vue'
import DataTable from '../../../Components/DataTable.vue'

interface Props {
    bans: PlayerBan[],
}
defineProps<Props>()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'status', label: 'Status' },
    { key: 'reason', label: 'Reason' },
    { key: 'created_at', label: 'Created At' },
]
</script>

<template>
    <DataTable :fields="fields" :rows="bans">
        <template #status="{ item }">
            <PlayerBanStatus :ban="item" />
        </template>
        <template #created_at="{ item }">
            {{ format(item.created_at) }}
        </template>
        <template #actions="{ item }">
            <Link :href="'/manage/player-bans/' + item.id + '/edit'">
                <FilledButton variant="secondary" size="sm">
                    Edit
                </FilledButton>
            </Link>
        </template>
    </DataTable>
</template>
