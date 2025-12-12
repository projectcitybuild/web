<script setup lang="ts">
import { distance, distanceFromNow, format } from '../../../../manage/Utilities/DateFormatter'
import { computed } from 'vue'
import DataTable from '../../../../manage/Components/DataTable.vue'
import { BanAppeal } from '../../../../manage/Data/BanAppeal'
import { BanAppealStatus } from '../../../../manage/Data/BanAppealStatus'
import FilledButton from '../../../../manage/Components/FilledButton.vue'
import Pill from '../../../../manage/Components/Pill.vue'
import { Link } from '@inertiajs/vue3'
import MinecraftAvatar from '../../../../manage/Components/MinecraftAvatar.vue'
import SvgIcon from '../../../../manage/Components/SvgIcon.vue'

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
    <DataTable :fields="fields" :rows="rows" :show-index="true" class="border-t border-gray-200">
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
            <Link :href="'/review/ban-appeals/' + item.id">
                <FilledButton v-if="item.status === BanAppealStatus.pending" variant="danger">
                    <SvgIcon icon="eye" />
                    Review
                </FilledButton>
                <FilledButton v-else variant="secondary">
                    View
                </FilledButton>
            </Link>
        </template>
    </DataTable>
</template>
