<script setup lang="ts">
import { AccountActivation } from '../../../Data/AccountActivation'
import { format } from '../../../Utilities/DateFormatter'

interface Props {
    activations: AccountActivation[],
}
defineProps<Props>()
</script>

<template>
    <table v-if="activations.length > 0" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-4 py-3">Id</th>
            <th scope="col" class="px-4 py-3">Created At</th>
            <th scope="col" class="px-4 py-3">Expires At</th>
        </tr>
        </thead>
        <tbody>
        <tr class="border-b dark:border-gray-700" v-for="activation in activations">
            <td class="px-4 py-3 text-gray-900 whitespace-nowrap dark:text-white">{{ activation.id }}</td>
            <td class="px-4 py-3">{{ format(activation.created_at) }}</td>
            <td class="px-4 py-3">{{ format(activation.expires_at) }}</td>
        </tr>
        </tbody>
    </table>
    <div v-else class="flex p-4">
        <span class="text-sm text-gray-400">No pending activations</span>
    </div>
</template>
