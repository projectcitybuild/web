<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import ErrorAlert from '../../../Components/ErrorAlert.vue'
import DateTimePicker from '../../../Components/DateTimePicker.vue'
import FilledButton from '../../../Components/FilledButton.vue'
import Spinner from '../../../Components/Spinner.vue'
import type { Account } from '../../../Data/Account'

interface Props {
    account?: Account,
    submit: Function,
}

const props = defineProps<Props>()

const form = useForm<Account>({
    username: props.account?.username,
    email: props.account?.email,
    password: null,
    created_at: props.account?.created_at
        ? new Date(props.account.created_at)
        : new Date(),
})

const isEdit = computed(() => props.account != null)

function submit() {
    props.submit(form)
}
</script>

<template>
    <form @submit.prevent="submit">
        <ErrorAlert v-if="form.hasErrors" :errors="form.errors" class="mb-4" />

        <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
            <div class="col-span-2">
                <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Email Address<span class="text-red-500">*</span>
                </label>
                <input
                    v-model="form.email"
                    id="email"
                    type="email"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                >
                <div v-if="form.errors.email" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.email }}
                </div>
            </div>
            <div class="col-span-2">
                <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Username<span class="text-red-500">*</span>
                </label>
                <input
                    v-model="form.username"
                    id="username"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                >
                <div v-if="form.errors.username" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.username }}
                </div>
            </div>
            <div class="col-span-2">
                <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Password<span v-if="!isEdit" class="text-red-500">*</span>
                </label>
                <input
                    v-model="form.password"
                    id="password"
                    type="password"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                >
                <div v-if="form.errors.password" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.password }}
                </div>
            </div>
            <div class="col-span-2">
                <label for="category" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Created At<span class="text-red-500">*</span>
                </label>
                <DateTimePicker
                    v-model="form.created_at"
                    @change="form.created_at = $event"
                />
                <div v-if="form.errors.created_at" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.created_at }}
                </div>
            </div>

            <span class="text-gray-500 text-sm col-span-2">
                Note: This will create an <strong>unactivated</strong> account.<br /><br />
                You will need to either force activate their account or send them their activation email afterwards.
            </span>
        </div>

        <FilledButton
            variant="primary"
            :disabled="form.processing"
            type="submit"
            class="mt-8"
        >
            <Spinner v-if="form.processing" />
            <template v-else>{{ isEdit ? 'Update' : 'Create' }}</template>
        </FilledButton>
    </form>
</template>
