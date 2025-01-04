<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import BackButton from '../../Components/BackButton.vue'
import ErrorAlert from '../../Components/ErrorAlert.vue'
import type { Badge } from '../../Data/Badge'
import BooleanCheck from '../../Components/BooleanCheck.vue'
import FilledButton from '../../Components/FilledButton.vue'

interface Props {
    success?: string,
    badges: Badge[],
    account_badge_ids: number[],
    account_id: number,
}
const props = defineProps<Props>()

const form = useForm({
    account_badge_ids: props.account_badge_ids,
})

function submit() {
    form.put('/manage/accounts/' + props.account_id + '/badges')
}
</script>

<template>
    <Head title="Manage Account Badges" />

    <Card>
        <div class="flex flex-row items-center justify-between p-4">
            <BackButton :href="'/manage/accounts/' + account_id"/>

            <FilledButton
                variant="primary"
                @click="submit"
                :disabled="!form.isDirty"
            >
                Update
            </FilledButton>
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
                    <th scope="col" class="px-4 py-3">Display Name</th>
                    <th scope="col" class="px-4 py-3">Unicode Icon</th>
                    <th scope="col" class="px-4 py-3">Hidden in List?</th>
                </tr>
                </thead>
                <tbody>
                <tr class="border-b dark:border-gray-700" v-for="badge in badges">
                    <td class="px-4 py-3">
                        <input
                            v-model="form.account_badge_ids"
                            :value="badge.id"
                            :id="badge.id"
                            type="checkbox"
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                        >
                    </td>
                    <td class="px-4 py-3 text-gray-900 whitespace-nowrap dark:text-white">{{ badge.id }}</td>
                    <td class="px-4 py-3">{{ badge.display_name }}</td>
                    <td class="px-4 py-3">{{ badge.unicode_icon }}</td>
                    <td class="px-4 py-3">
                        <BooleanCheck :value="badge.list_hidden" />
                    </td>
                </tr>
                </tbody>
            </table>
        </Card>
    </section>
</template>
