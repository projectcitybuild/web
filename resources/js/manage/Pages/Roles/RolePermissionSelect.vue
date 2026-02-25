<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import BackButton from '../../Components/BackButton.vue'
import ErrorAlert from '../../Components/ErrorAlert.vue'
import FilledButton from '../../Components/FilledButton.vue'
import OutlinedButton from '../../Components/OutlinedButton.vue'
import { Permission } from '../../Data/Permission'

interface Props {
    success?: string,
    permissions: Permission[],
    role_permission_ids: number[],
    role_id: number,
}
const props = defineProps<Props>()

const form = useForm({
    role_permission_ids: props.role_permission_ids,
})

function submit() {
    form.put('/manage/roles/' + props.role_id + '/permissions')
}

function selectAll() {
    form.role_permission_ids = props.permissions.map((it) => it.id)
}

function deselectAll() {
    form.role_permission_ids = []
}
</script>

<template>
    <Head title="Manage Role Permissions" />

    <Card>
        <div class="flex flex-row items-center justify-between p-4">
            <BackButton href="/manage/roles"/>

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
                    Note: Roles do not inherit permissions of other roles
                </span>
            </Card>

            <FilledButton variant="secondary" @click="selectAll" :disabled="form.role_permission_ids.length == props.permissions.length" class="mt-4 w-full">
                Select All
            </FilledButton>

            <FilledButton variant="secondary" @click="deselectAll" :disabled="form.role_permission_ids.length == 0" class="mt-4 w-full">
                Deselect All
            </FilledButton>
        </section>

        <section class="grow">
            <Card>
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-3">Select</th>
                            <th scope="col" class="px-4 py-3">Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b dark:border-gray-700" v-for="permission in permissions">
                            <td class="px-4 py-3">
                                <input
                                    v-model="form.role_permission_ids"
                                    :value="permission.id"
                                    :id="permission.id"
                                    type="checkbox"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                >
                            </td>
                            <td class="px-4 py-3">{{ permission.name }}</td>
                        </tr>
                    </tbody>
                </table>
            </Card>
        </section>
    </div>
</template>
