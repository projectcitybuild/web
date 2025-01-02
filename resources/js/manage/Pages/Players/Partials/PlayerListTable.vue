<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { format } from '../../../Utilities/DateFormatter'
import type { Player } from '../../../Data/Player'
import MinecraftAvatar from '../../../Components/MinecraftAvatar.vue'

interface Props {
    players: Player[],
}
defineProps<Props>()
</script>

<template>
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-4 py-3">#</th>
            <th scope="col" class="px-4 py-3">Id</th>
            <th scope="col" class="px-4 py-3">Skin</th>
            <th scope="col" class="px-4 py-3">UUID</th>
            <th scope="col" class="px-4 py-3">Alias</th>
            <th scope="col" class="px-4 py-3">Linked Account</th>
            <th scope="col" class="px-4 py-3">Created At</th>
            <th scope="col" class="px-4 py-3">Updated At</th>
        </tr>
        </thead>
        <tbody>
        <tr class="border-b dark:border-gray-700" v-for="(player, index) in players">
            <th scope="row" class="px-4 py-3 text-gray-400 whitespace-nowrap dark:text-white">#{{ index + 1 }}</th>
            <td class="px-4 py-3 text-gray-900 whitespace-nowrap dark:text-white">{{ player.player_minecraft_id }}</td>
            <td class="px-4 py-3">
                <MinecraftAvatar :alias="player.alias ?? player.uuid" :size="16"/>
            </td>
            <td class="px-4 py-3">
                <Link
                    :href="'/manage/players/' + player.player_minecraft_id"
                    class="text-blue-500"
                >
                    {{ player.uuid ?? '-' }}
                </Link>
            </td>
            <td class="px-4 py-3">{{ player.alias }}</td>
            <td class="px-4 py-3">
                <Link
                    v-if="player.account"
                    :href="'/manage/accounts/' + player.account?.account_id"
                    class="text-blue-500"
                >
                    {{ player.account?.username }}
                </Link>
                <span v-else>-</span>
            </td>
            <td class="px-4 py-3">{{ format(player.created_at) }}</td>
            <td class="px-4 py-3">{{ format(player.updated_at) }}</td>
        </tr>
        </tbody>
    </table>
</template>
