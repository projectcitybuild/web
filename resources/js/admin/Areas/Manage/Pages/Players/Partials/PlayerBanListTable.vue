<script setup lang="ts">
import { Routes } from '../../../../../Navigation/Routes'
import LinkButton from '../../../../../Components/LinkButton.vue'
import { format } from '../../../../../Utilities/DateFormatter'
import { Link } from '@inertiajs/vue3'
import PlayerBanStatus from '../../PlayerBans/Partials/PlayerBanStatus.vue'
import OutlinedButton from '../../../../../Components/OutlinedButton.vue'
import SvgIcon from '../../../../../Components/SvgIcon.vue'
import { Icons } from '../../../../../Components/SvgIcon.vue'
import DataTable from '../../../../../Components/DataTable.vue'

interface Props {
    bans: App.Http.Data.AdminGamePlayerBanData[],
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
            <LinkButton :href="Routes.manage.playerBans.edit(item.id)" variant="outline" size="sm">
                <SvgIcon :svg="Icons.pencil" />
                Edit
            </LinkButton>
        </template>
    </DataTable>
</template>
