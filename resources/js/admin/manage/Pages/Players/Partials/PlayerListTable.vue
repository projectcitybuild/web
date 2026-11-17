<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { format } from '../../../Utilities/DateFormatter'
import type { Player } from '../../../Data/Player'
import MinecraftAvatar from '../../../Components/MinecraftAvatar.vue'
import { computed } from 'vue'
import DataTable from '../../../Components/DataTable.vue'
import usePermissions from '../../../Composables/usePermissions'

interface Props {
    players: Player[],
}
const props = defineProps<Props>()

const { can } = usePermissions()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'skin', label: 'Skin' },
    { key: 'uuid', label: 'UUID' },
    { key: 'alias', label: 'Last Known Alias' },
    { key: 'linked_account', label: 'Linked Account' },
    { key: 'created_at', label: 'Created At' },
    { key: 'updated_at', label: 'Updated At' },
    { key: 'last_seen_at', label: 'Last Seen At' }
]
const rows = computed(
    () => props.players.map((player) => ({
        ...player,
        created_at: format(player.created_at),
        updated_at: format(player.updated_at),
        last_seen_at: format(player.last_seen_at),
    }))
)
</script>

<template>
    <DataTable :fields="fields" :rows="rows" :show-index="true">
        <template #skin="{ item }">
            <MinecraftAvatar :uuid="item.uuid" :size="32"/>
        </template>

        <template #uuid="{ item }">
            <Link
                :href="'/manage/players/' + item.id"
                class="text-primary font-semibold underline-offset-2 underline focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus"
            >
                {{ item.uuid }}
            </Link>
        </template>

        <template #linked_account="{ item }">
            <Link
                v-if="item.account"
                :href="'/manage/accounts/' + item.account?.id"
                class="text-primary font-semibold underline-offset-2 underline focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus"
            >
                {{ item.account?.username }}
            </Link>
        </template>
    </DataTable>
</template>
