<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Group } from '../../../Data/Group'
import BooleanCheck from '../../../Components/BooleanCheck.vue'

interface Props {
    groups: Group[],
}

defineProps<Props>()
</script>

<template>
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-4 py-3">Id</th>
            <th scope="col" class="px-4 py-3">Name</th>
            <th scope="col" class="px-4 py-3">Members</th>
            <th scope="col" class="px-4 py-3">Alias</th>
            <th scope="col" class="px-4 py-3">Minecraft Name</th>
            <th scope="col" class="px-4 py-3">Group Type</th>
            <th scope="col" class="px-4 py-3">Display Priority</th>
            <th scope="col" class="px-4 py-3">Is Default?</th>
            <th scope="col" class="px-4 py-3">Is Admin?</th>
            <th scope="col" class="px-4 py-3">Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr class="border-b dark:border-gray-700" v-for="group in groups">
            <td class="px-4 py-3 text-gray-900 whitespace-nowrap dark:text-white">{{ group.group_id }}</td>
            <td class="px-4 py-3">{{ group.name }}</td>
            <td class="px-4 py-3">
                <Link
                    :href="'/manage/groups/' + group.group_id + '/accounts'"
                    class="text-blue-500 font-bold"
                >
                    {{ group.accounts_count }}
                </Link></td>
            <td class="px-4 py-3">{{ group.alias }}</td>
            <td class="px-4 py-3">{{ group.minecraft_name }}</td>
            <td class="px-4 py-3">{{ group.group_type }}</td>
            <td class="px-4 py-3">{{ group.display_priority }}</td>
            <td class="px-4 py-3">
                <BooleanCheck :value="group.is_default"/>
            </td>
            <td class="px-4 py-3">
                <BooleanCheck :value="group.is_admin"/>
            </td>
            <td class="px-4 py-3 text-right">
                <Link
                    :href="'/manage/groups/' + group.group_id + '/edit'"
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
