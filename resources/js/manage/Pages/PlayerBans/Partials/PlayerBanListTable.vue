<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import MinecraftAvatar from '../../../Components/MinecraftAvatar.vue'
import { PlayerBan } from '../../../Data/PlayerBan'
import { format } from '../../../Utilities/DateFormatter'
import PlayerBanStatus from './PlayerBanStatus.vue'

interface Props {
    bans: PlayerBan[],
}

defineProps<Props>()
</script>

<template>
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-4 py-3">#</th>
            <th scope="col" class="px-4 py-3">Id</th>
            <th scope="col" class="px-4 py-3">Status</th>
            <th scope="col" class="px-4 py-3">Player</th>
            <th scope="col" class="px-4 py-3">Reason</th>
            <th scope="col" class="px-4 py-3">Created At</th>
            <th scope="col" class="px-4 py-3">Expires At</th>
            <th scope="col" class="px-4 py-3">
                <span class="sr-only">Actions</span>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr class="border-b dark:border-gray-700" v-for="(ban, index) in bans">
            <th scope="row" class="px-4 py-3 text-gray-400 whitespace-nowrap dark:text-white">#{{ index + 1 }}</th>
            <td class="px-4 py-3 text-gray-900 whitespace-nowrap dark:text-white">{{ ban.id }}</td>
            <td class="px-4 py-3">
                <PlayerBanStatus :ban="ban" />
            </td>
            <td class="px-4 py-3 flex flex-row items-center gap-2">
                <MinecraftAvatar :uuid="ban.banned_player.uuid" :size="16"/>
                <Link
                    :href="'/manage/players/' + ban.banned_player.player_minecraft_id"
                    class="text-blue-500"
                >
                    {{ ban.banned_player.alias ?? '-' }}
                </Link>
            </td>
            <td class="px-4 py-3">{{ ban.reason }}</td>
            <td class="px-4 py-3">{{ format(ban.created_at) }}</td>
            <td class="px-4 py-3">{{ format(ban.expires_at) ?? '-' }}</td>
            <td class="px-4 py-3 text-right">
                <Link
                    :href="'/manage/player-bans/' + ban.id + '/edit'"
                    class="
                        py-2 px-4 rounded-md
                        bg-gray-500 text-white
                         hover:bg-gray-600
                    "
                >
                    Edit
                </Link>
            </td>
        </tr>
        </tbody>
    </table>
</template>
