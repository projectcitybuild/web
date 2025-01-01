<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Warp } from '../../../Data/Warp'
import { format } from '../../../Utilities/DateFormatter'

interface Props {
    warps: Warp[],
}

defineProps<Props>()
</script>

<template>
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-4 py-3">#</th>
            <th scope="col" class="px-4 py-3">Id</th>
            <th scope="col" class="px-4 py-3">Name</th>
            <th scope="col" class="px-4 py-3">World</th>
            <th scope="col" class="px-4 py-3">x</th>
            <th scope="col" class="px-4 py-3">y</th>
            <th scope="col" class="px-4 py-3">z</th>
            <th scope="col" class="px-4 py-3">Pitch</th>
            <th scope="col" class="px-4 py-3">Yaw</th>
            <th scope="col" class="px-4 py-3">Created At</th>
            <th scope="col" class="px-4 py-3">
                <span class="sr-only">Actions</span>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr class="border-b dark:border-gray-700" v-for="(warp, index) in warps">
            <th scope="row" class="px-4 py-3 text-gray-400 whitespace-nowrap dark:text-white">#{{ index + 1 }}</th>
            <td class="px-4 py-3">{{ warp.id }}</td>
            <td class="px-4 py-3 whitespace-nowrap text-gray-900 dark:text-white">{{ warp.name }}</td>
            <td class="px-4 py-3">{{ warp.world }}</td>
            <td class="px-4 py-3">{{ warp.x }}</td>
            <td class="px-4 py-3">{{ warp.y }}</td>
            <td class="px-4 py-3">{{ warp.z }}</td>
            <td class="px-4 py-3">{{ warp.pitch }}</td>
            <td class="px-4 py-3">{{ warp.yaw }}</td>
            <td class="px-4 py-3">{{ format(warp.created_at) }}</td>
            <td class="px-4 py-3 text-right">
                <Link
                    :href="'/manage/minecraft/warps/' + warp.id + '/edit'"
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
