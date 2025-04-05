<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import MinecraftAvatar from '../../../Components/MinecraftAvatar.vue'
import { PlayerBan } from '../../../Data/PlayerBan'
import { format } from '../../../Utilities/DateFormatter'
import PlayerBanStatus from './PlayerBanStatus.vue'
import FilledButton from '../../../Components/FilledButton.vue'
import { computed } from 'vue'
import DataTable from '../../../Components/DataTable.vue'

interface Props {
    bans: PlayerBan[],
}
const props = defineProps<Props>()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'status', label: 'Status' },
    { key: 'player', label: 'Player' },
    { key: 'reason', label: 'Reason' },
    { key: 'created_at', label: 'Created At' },
    { key: 'expires_at', label: 'Expires At' },
]
const rows = computed(
    () => props.bans.map((ban) => ({
        ...ban,
        created_at: format(ban.created_at),
        expires_at: format(ban.expires_at),
    }))
)
</script>

<template>
    <DataTable :fields="fields" :rows="rows" :show-index="true" class="border-t border-gray-200">
        <template #status="{ item }">
            <PlayerBanStatus :ban="item" />
        </template>

        <template #player="{ item }">
            <div class="px-4 py-3 flex flex-row items-center gap-2">
                <MinecraftAvatar :uuid="item.banned_player.uuid" :size="16"/>
                <Link
                    :href="'/manage/players/' + item.banned_player.player_minecraft_id"
                    class="text-blue-500"
                >
                    {{ item.banned_player.alias ?? '-' }}
                </Link>
            </div>
        </template>

        <template #actions="{ item }">
            <Link :href="'/manage/player-bans/' + item.id + '/edit'">
                <FilledButton variant="secondary">
                    Edit
                </FilledButton>
            </Link>
        </template>
    </DataTable>
</template>
