<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { format } from '../../../Utilities/DateFormatter'
import type { Player } from '../../../Data/Player'
import MinecraftAvatar from '../../../Components/MinecraftAvatar.vue'
import { computed } from 'vue'
import DataTable from '../../../Components/DataTable.vue'

interface Props {
    players: Player[],
}
const props = defineProps<Props>()

const fields = [
    { key: 'player_minecraft_id', label: 'ID' },
    { key: 'skin', label: 'Skin' },
    { key: 'uuid', label: 'UUID' },
    { key: 'alias', label: 'Last Known Alias' },
    { key: 'linked_account', label: 'Linked Account' },
    { key: 'created_at', label: 'Created At' },
    { key: 'updated_at', label: 'Updated At' },
]
const rows = computed(
    () => props.players.map((player) => ({
        ...player,
        created_at: format(player.created_at),
        updated_at: format(player.updated_at),
    }))
)
</script>

<template>
    <DataTable :fields="fields" :rows="rows" :show-index="true">
        <template #skin="{ item }">
            <MinecraftAvatar :uuid="item.uuid" :size="16"/>
        </template>

        <template #uuid="{ item }">
            <Link
                :href="'/manage/players/' + item.player_minecraft_id"
                class="text-blue-500"
            >
                {{ item.uuid }}
            </Link>
        </template>

        <template #linked_account="{ item }">
            <Link
                v-if="item.account"
                :href="'/manage/accounts/' + item.account?.account_id"
                class="text-blue-500"
            >
                {{ item.account?.username }}
            </Link>
        </template>
    </DataTable>
</template>
