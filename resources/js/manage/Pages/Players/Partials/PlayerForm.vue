<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import ErrorAlert from '../../../Components/ErrorAlert.vue'
import DateTimePicker from '../../../Components/DateTimePicker.vue'
import type { Player } from '../../../Data/Player'
import PlayerPicker from '../../../Components/PlayerPicker.vue'
import FilledButton from '../../../Components/FilledButton.vue'
import Spinner from '../../../Components/Spinner.vue'

interface Props {
    player?: Player,
    submit: Function,
}

const props = defineProps<Props>()

const form = useForm<Player>({
    uuid: props.player?.uuid,
    alias: props.player?.alias,
    nickname: props.player?.nickname,
    account_id: props.player?.account_id,
    created_at: props.player?.created_at
        ? new Date(props.player.created_at)
        : new Date(),
})

const isEdit = computed(() => props.player != null)

function submit() {
    props.submit(form)
}
</script>

<template>
    <form @submit.prevent="submit">
        <ErrorAlert v-if="form.hasErrors" :errors="form.errors" class="mb-4"/>

        <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
            <div class="col-span-2">
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Player<span class="text-red-500">*</span>
                    </label>
                    <PlayerPicker
                        v-model:uuid="form.uuid"
                        v-model:alias="form.alias"
                    />
                </div>
                <div class="text-gray-500 text-sm mt-4">or</div>
            </div>
            <div class="col-span-2">
                <label for="uuid" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    UUID<span class="text-red-500">*</span>
                </label>
                <input
                    v-model="form.uuid"
                    id="uuid"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                >
                <div v-if="form.errors.uuid" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.uuid }}
                </div>
            </div>
            <div class="col-span-2">
                <label for="alias" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Last Known Alias<span class="text-red-500">*</span>
                </label>
                <input
                    v-model="form.alias"
                    id="alias"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                >
                <div v-if="form.errors.alias" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.alias }}
                </div>
            </div>
            <div class="col-span-2">
                <label for="nickname" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    In-Game Nickname
                </label>
                <input
                    v-model="form.nickname"
                    id="nickname"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                >
                <div v-if="form.errors.nickname" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.nickname }}
                </div>
            </div>

            <hr class="col-span-2" />

            <div class="col-span-2">
                <label for="category" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    First Seen<span class="text-red-500">*</span>
                </label>
                <DateTimePicker
                    v-model="form.created_at"
                    @change="form.created_at = $event"
                />
                <div v-if="form.errors.created_at" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.created_at }}
                </div>
            </div>
        </div>

        <FilledButton
            type="submit"
            variant="primary"
            :disabled="form.processing"
            class="mt-8"
        >
            <Spinner v-if="form.processing" />
            <template v-else>{{ isEdit ? 'Update' : 'Create' }}</template>
        </FilledButton>
    </form>
</template>
