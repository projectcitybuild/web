<script setup lang="ts">
import { format } from '../../../Utilities/DateFormatter'
import { Home } from '../../../Data/Home';

interface Props {
    homes: Home[],
}
defineProps<Props>()
</script>

<template>
    <table class="min-w-full whitespace-nowrap text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-4 py-3">Id</th>
                <th scope="col" class="px-4 py-3">Name</th>
                <th scope="col" class="px-4 py-3">World</th>
                <th scope="col" class="px-4 py-3">x</th>
                <th scope="col" class="px-4 py-3">y</th>
                <th scope="col" class="px-4 py-3">z</th>
                <th scope="col" class="px-4 py-3">Pitch</th>
                <th scope="col" class="px-4 py-3">Yaw</th>
                <th scope="col" class="px-4 py-3">Created At</th>
            </tr>
        </thead>
        <tbody>
            <tr class="border-b dark:border-gray-700" v-for="home in homes">
                <td class="px-4 py-3 text-gray-900 whitespace-nowrap dark:text-white">{{ home.id }}</td>
                <td class="px-4 py-3">{{ home.name }}</td>
                <td class="px-4 py-3">{{ home.world }}</td>
                <td class="px-4 py-3">{{ Math.round(home.x) }}</td>
                <td class="px-4 py-3">{{ Math.round(home.y) }}</td>
                <td class="px-4 py-3">{{ Math.round(home.z) }}</td>
                <td class="px-4 py-3">{{ Number(home.pitch).toFixed(2) }}</td>
                <td class="px-4 py-3">{{ Number(home.yaw).toFixed(2) }}</td>
                <td class="px-4 py-3">{{ format(home.created_at) }}</td>
            </tr>
        </tbody>
    </table>
</template>
