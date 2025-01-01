<script setup lang="ts">
import { format } from '../../../Utilities/DateFormatter'
import { Link } from '@inertiajs/vue3'
import { PlayerBan } from '../../../Data/PlayerBan'
import PlayerBanStatus from '../../PlayerBans/Partials/PlayerBanStatus.vue'

interface Props {
    bans: PlayerBan[],
}
defineProps<Props>()
</script>

<template>
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-4 py-3">Id</th>
            <th scope="col" class="px-4 py-3">Status</th>
            <th scope="col" class="px-4 py-3">Reason</th>
            <th scope="col" class="px-4 py-3">Created At</th>
            <th scope="col" class="px-4 py-3">Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr class="border-b dark:border-gray-700" v-for="ban in bans">
            <td class="px-4 py-3 text-gray-900 whitespace-nowrap dark:text-white">{{ ban.id }}</td>
            <td class="px-4 py-3">
                <PlayerBanStatus :ban="ban" />
            </td>
            <td class="px-4 py-3">{{ ban.reason }}</td>
            <td class="px-4 py-3">{{ format(ban.created_at) }}</td>
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
