<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import type { ServerToken } from '../../../Data/ServerToken'

interface Props {
    tokens: ServerToken[],
}
const props = defineProps<Props>()
</script>

<template>
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-4 py-3">Id</th>
            <th scope="col" class="px-4 py-3">Token</th>
            <th scope="col" class="px-4 py-3">Description</th>
            <th scope="col" class="px-4 py-3">Created At</th>
            <th scope="col" class="px-4 py-3">Updated At</th>
            <th scope="col" class="px-4 py-3">
                <span class="sr-only">Actions</span>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr class="border-b dark:border-gray-700 text-sm" v-for="token in tokens">
            <td class="px-4 py-3 text-gray-900 whitespace-nowrap dark:text-white">{{ token.id }}</td>
            <td class="px-4 py-3 text-black">{{ token.token }}</td>
            <td class="px-4 py-3">{{ token.description }}</td>
            <td class="px-4 py-3">{{ token.created_at }}</td>
            <td class="px-4 py-3">{{ token.updated_at }}</td>
            <td class="px-4 py-3 text-right">
                <Link
                    :href="'/manage/server-tokens/' + token.id + '/edit'"
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
