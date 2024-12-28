<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { compareAsc, format } from 'date-fns'
import MinecraftAvatar from '../../../Components/MinecraftAvatar.vue'
import { PlayerBan } from '../../../Data/PlayerBan'

interface Props {
    bans: PlayerBan[],
}
defineProps<Props>()

function formatted(dateString: string) {
    if (!dateString) return null
    const date = new Date(dateString)
    return format(date, 'yyyy/MM/dd h:ma')
}

function isActive(ban: PlayerBan) {
    if (ban.unbanned_at) return false
    if (!ban.expires_at) return true

    const expiresAt = new Date(ban.expires_at)
    const now = new Date()

    // 1 = left date is after right date
    return (compareAsc(expiresAt, now) == 1)
}
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
                <span v-if="isActive(ban)" class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Active</span>
                <span v-else class="py-1 px-2 bg-gray-200 rounded-md text-xs">Expired</span>
            </td>
            <td class="px-4 py-3 font-bold flex flex-row items-center gap-2">
                <MinecraftAvatar :alias="ban.banned_player.alias" :size="16" />
                {{ ban.banned_player.alias ?? '-' }}
            </td>
            <td class="px-4 py-3">{{ ban.reason }}</td>
            <td class="px-4 py-3">{{ formatted(ban.created_at) }}</td>
            <td class="px-4 py-3">{{ formatted(ban.expires_at) ?? '-' }}</td>
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
