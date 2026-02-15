<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import { Role } from '../../Data/Role'
import BackButton from '../../Components/BackButton.vue'
import ErrorAlert from '../../Components/ErrorAlert.vue'
import FilledButton from '../../Components/FilledButton.vue'

interface Props {
    success?: string,
    roles: Role[],
    account_role_ids: number[],
    account_id: number,
}
const props = defineProps<Props>()

const form = useForm({
    account_role_ids: props.account_role_ids,
})

function submit() {
    form.put('/manage/accounts/' + props.account_id + '/roles')
}
</script>

<template>
    <Head title="Manage Account Roles" />

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
                    Note: Removing all roles will automatically assign this user to the default (Member) role
                </span>
            </Card>

            <FilledButton
                variant="secondary"
                @click="form.account_role_ids = []"
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
                            <th scope="col" class="px-4 py-3">Role Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b dark:border-gray-700" v-for="role in roles">
                            <td class="px-4 py-3">
                                <input
                                    v-model="form.account_role_ids"
                                    :value="role.id"
                                    :id="role.id"
                                    type="checkbox"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                >
                            </td>
                            <td class="px-4 py-3 text-gray-900 whitespace-nowrap dark:text-white">{{ role.id }}</td>
                            <td class="px-4 py-3">{{ role.name }}</td>
                            <td class="px-4 py-3">{{ role.role_type }}</td>
                        </tr>
                    </tbody>
                </table>
            </Card>
        </section>
    </div>
</template>
