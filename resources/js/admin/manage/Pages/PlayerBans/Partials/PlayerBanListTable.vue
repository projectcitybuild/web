<script setup lang="ts">
import LinkButton from '../../../../shared/Components/LinkButton.vue'
import { Link } from '@inertiajs/vue3'
import MinecraftAvatar from '../../../Components/MinecraftAvatar.vue'
import { PlayerBan } from '../../../Data/PlayerBan'
import { format } from '../../../Utilities/DateFormatter'
import PlayerBanStatus from './PlayerBanStatus.vue'
import SvgIcon from '../../../Components/SvgIcon.vue'
import { Icons } from '../../../Icons'
import { computed } from 'vue'
import DataTable from '../../../Components/DataTable.vue'
import usePermissions from '../../../Composables/usePermissions'

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

const { can } = usePermissions()
</script>

<template>
    <DataTable :fields="fields" :rows="rows" :show-index="true">
        <template #status="{ item }">
            <PlayerBanStatus :ban="item" />
        </template>

        <template #player="{ item }">
            <div class="px-4 py-3 flex flex-row items-center gap-2">
                <MinecraftAvatar :uuid="item.banned_player.uuid" :size="16"/>
                <Link
                    :href="'/manage/players/' + item.banned_player.id"
                    class="text-primary"
                >
                    {{ item.banned_player.alias ?? '-' }}
                </Link>
            </div>
        </template>

        <template #actions="{ item }">
            <LinkButton :href="'/manage/player-bans/' + item.id + '/edit'" v-if="can('web.manage.uuid_bans.edit')" variant="outline" size="sm">
                <SvgIcon :svg="Icons.pencil" />
                Edit
            </LinkButton>
        </template>
    </DataTable>
</template>
