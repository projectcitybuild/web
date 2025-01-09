<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import { Group } from '../../Data/Group'
import BackButton from '../../Components/BackButton.vue'
import ErrorAlert from '../../Components/ErrorAlert.vue'
import FilledButton from '../../Components/FilledButton.vue'

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

            <FilledButton variant="primary" @click="submit">
                Update
            </FilledButton>
        </div>
    </Card>

    <ErrorAlert v-if="form.hasErrors" :errors="form.errors" class="mb-4"/>

    <div class="flex flex-col md:flex-row gap-2 mt-4">
        <section class="md:max-w-80">
            <Card class="p-4">
                <span class="text-sm text-gray-500">
                    Note: Removing all groups will automatically assign this user to the default (Member) group
                </span>
            </Card>

            <FilledButton
                variant="secondary"
                @click="form.account_group_ids = []"
                class="mt-4 w-full"
            >
                Clear All Selections
            </FilledButton>
        </section>

        <section class="grow">
            <Card>
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
    </div>
</template>
