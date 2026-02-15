<script setup lang="ts">
import type { Role } from '../../../Data/Role'

interface Props {
    roles: Role[],
}
defineProps<Props>()
</script>

<template>
    <table v-if="roles.length > 0" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-4 py-3">Id</th>
            <th scope="col" class="px-4 py-3">Name</th>
            <th scope="col" class="px-4 py-3">Role Type</th>
        </tr>
        </thead>
        <tbody>
        <tr class="border-b dark:border-gray-700" v-for="role in roles">
            <td class="px-4 py-3 text-gray-900 whitespace-nowrap dark:text-white">{{ role.id }}</td>
            <td class="px-4 py-3">{{ role.name }}</td>
            <td class="px-4 py-3">{{ role.role_type }}</td>
        </tr>
        </tbody>
    </table>
    <div v-else class="flex p-4">
        <span class="text-sm text-gray-400">No roles</span>
    </div>
</template>
