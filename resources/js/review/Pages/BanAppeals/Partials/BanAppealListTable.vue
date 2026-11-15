<script setup lang="ts">
import { distance, distanceFromNow, format } from '../../../../manage/Utilities/DateFormatter'
import { computed } from 'vue'
import DataTable from '../../../../manage/Components/DataTable.vue'
import { BanAppeal } from '../../../../manage/Data/BanAppeal'
import { BanAppealStatus } from '../../../../manage/Data/BanAppealStatus'
import Pill from '../../../../manage/Components/Pill.vue'
import { Link } from '@inertiajs/vue3'
import MinecraftAvatar from '../../../../manage/Components/MinecraftAvatar.vue'
import SvgIcon from '../../../../manage/Components/SvgIcon.vue'
import { Icons } from '../../../../manage/Icons'

interface Props {
    banAppeals: BanAppeal[],
}
const props = defineProps<Props>()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'status', label: 'Status' },
    { key: 'waiting_time', label: 'Waiting Time' },
    { key: 'player', label: 'Player' },
    { key: 'created_at', label: 'Created At' },
    { key: 'decided_at', label: 'Reviewed At' },
]
const rows = computed(
    () => props.banAppeals.map((banAppeal) => ({
        ...banAppeal,
        waiting_time: banAppeal.decided_at == null
            ? distanceFromNow(banAppeal.created_at)
            : distance(banAppeal.created_at, banAppeal.decided_at),
        created_at: format(banAppeal.created_at),
        updated_at: format(banAppeal.updated_at),
        decided_at: format(banAppeal.decided_at) ?? '-',
    }))
)
</script>

<template>
    <DataTable :fields="fields" :rows="rows" :show-index="true" class="border-t border-border">
        <template #status="{ item }">
            <Pill v-if="item.status === 0" variant="danger">Awaiting Review</Pill>
            <Pill v-if="item.status === 1" variant="secondary">Unbanned</Pill>
            <Pill v-if="item.status === 2" variant="secondary">Temp Banned</Pill>
            <Pill v-if="item.status === 3" variant="secondary">Denied</Pill>
        </template>

        <template #player="{ item }">
            <div class="flex items-center gap-2" v-if="item.game_player_ban">
                <MinecraftAvatar :uuid="item.game_player_ban.banned_player.uuid" :size="16" />
                {{ item.game_player_ban.banned_player.alias }}
            </div>
            <div class="flex items-center gap-2" v-else>
                {{ item.email }}
            </div>
        </template>

        <template #actions="{ item }">
            <Link
                :href="'/review/ban-appeals/' + item.id"
                class="inline-flex min-h-10 items-center justify-center gap-2 rounded-[var(--radius-md)] px-4 text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus focus-visible:ring-offset-2 focus-visible:ring-offset-surface motion-reduce:transition-none"
                :class="item.status === BanAppealStatus.pending
                    ? 'bg-danger text-danger-foreground hover:bg-danger-hover active:bg-danger-hover'
                    : 'bg-surface-muted text-foreground hover:bg-surface-hover active:bg-surface-hover'"
            >
                <SvgIcon v-if="item.status === BanAppealStatus.pending" :svg="Icons.eye" aria-hidden="true" />
                {{ item.status === BanAppealStatus.pending ? 'Review' : 'View' }}
            </Link>
        </template>
    </DataTable>
</template>
