<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Badge } from '../../../Data/Badge'
import { format } from '../../../Utilities/DateFormatter'

interface Props {
    badges: Badge[],
}
defineProps<Props>()
</script>

<template>
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-4 py-3">#</th>
            <th scope="col" class="px-4 py-3">Id</th>
            <th scope="col" class="px-4 py-3">Display Name</th>
            <th scope="col" class="px-4 py-3">Unicode Icon</th>
            <th scope="col" class="px-4 py-3">Hidden in List</th>
            <th scope="col" class="px-4 py-3">Created At</th>
            <th scope="col" class="px-4 py-3">
                <span class="sr-only">Actions</span>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr class="border-b dark:border-gray-700" v-for="(badge, index) in badges">
            <th scope="row" class="px-4 py-3 text-gray-400 whitespace-nowrap dark:text-white">#{{ index + 1 }}</th>
            <td class="px-4 py-3 text-gray-900 whitespace-nowrap dark:text-white">{{ badge.id }}</td>
            <td class="px-4 py-3">{{ badge.display_name }}</td>
            <td class="px-4 py-3">{{ badge.unicode_icon }}</td>
            <td class="px-4 py-3">{{ badge.list_hidden ? 'Yes' : 'No' }}</td>
            <td class="px-4 py-3">{{ format(badge.created_at) }}</td>
            <td class="px-4 py-3 text-right">
                <Link
                    :href="'/manage/badges/' + badge.id + '/edit'"
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
