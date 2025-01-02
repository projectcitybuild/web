<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import { Group } from '../../Data/Group'
import BackButton from '../../Components/BackButton.vue'
import ErrorAlert from '../../Components/ErrorAlert.vue'

interface Props {
    success?: string,
    groups: Group[],
    account_group_ids: number[],
    account_id: number,
}
const props = defineProps<Props>()

const form = useForm({
    account_group_ids: props.account_group_ids,
})

function submit() {
    form.put('/manage/accounts/' + props.account_id + '/groups')
}
</script>

<template>
    <Head title="Manage Account Groups" />

    <Card>
        <div class="flex flex-row items-center justify-between p-4">
            <BackButton :href="'/manage/accounts/' + account_id"/>

            <button
                type="button"
                @click="submit"
                class="
                    flex flex-row items-center justify-center gap-2 px-4 py-2 rounded-lg
                    text-sm text-white bg-blue-700
                    hover:bg-primary-800
                    focus:ring-4 focus:ring-blue-300
                    dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800
                "
            >
                Update
            </button>
        </div>
    </Card>

    <section class="mx-auto max-w-screen-xl mt-4 bg-gray-50 dark:bg-gray-900 p-3 sm:p-5">
        <Card>
            <ErrorAlert v-if="form.hasErrors" :errors="form.errors" class="mb-4"/>

            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-4 py-3">Select</th>
                    <th scope="col" class="px-4 py-3">Id</th>
                    <th scope="col" class="px-4 py-3">Name</th>
                    <th scope="col" class="px-4 py-3">Group Type</th>
                </tr>
                </thead>
                <tbody>
                <tr class="border-b dark:border-gray-700" v-for="group in groups">
                    <td class="px-4 py-3">
                        <input
                            v-model="form.account_group_ids"
                            :value="group.group_id"
                            :id="group.group_id"
                            type="checkbox"
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                        >
                    </td>
                    <td class="px-4 py-3 text-gray-900 whitespace-nowrap dark:text-white">{{ group.group_id }}</td>
                    <td class="px-4 py-3">{{ group.name }}</td>
                    <td class="px-4 py-3">{{ group.group_type }}</td>
                </tr>
                </tbody>
            </table>
        </Card>
    </section>
</template>
