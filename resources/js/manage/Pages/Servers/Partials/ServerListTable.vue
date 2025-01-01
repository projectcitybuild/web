<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import type { Server } from '../../../Data/Server'

interface Props {
    servers: Server[],
}

const props = defineProps<Props>()
</script>

<template>
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-4 py-3">Id</th>
            <th scope="col" class="px-4 py-3">Name</th>
            <th scope="col" class="px-4 py-3">IP Address</th>
            <th scope="col" class="px-4 py-3">Port</th>
            <th scope="col" class="px-4 py-3">Web Port</th>
            <th scope="col" class="px-4 py-3">
                <span class="sr-only">Actions</span>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr class="border-b dark:border-gray-700 text-sm" v-for="server in servers">
            <td class="px-4 py-3 text-gray-900 whitespace-nowrap dark:text-white">{{ server.server_id }}</td>
            <td class="px-4 py-3 text-black">{{ server.name }}</td>
            <td class="px-4 py-3">{{ server.ip }}</td>
            <td class="px-4 py-3">{{ server.port }}</td>
            <td class="px-4 py-3">{{ server.web_port ?? '-' }}</td>
            <td class="px-4 py-3 text-right">
                <Link
                    :href="'/manage/servers/' + server.server_id + '/edit'"
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
