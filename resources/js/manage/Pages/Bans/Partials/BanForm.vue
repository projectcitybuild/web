<script setup>
import DateTimePicker from '../../../Components/DateTimePicker.vue'
import PlayerPicker from '../../../Components/PlayerPicker.vue'
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    ban: Object|null,
    submit: Function,
})

const form = useForm({
    banned_uuid: props.ban?.banned_player.uuid,
    banned_alias: props.ban?.banned_player.alias,
    banner_uuid: props.ban?.banner_player?.uuid,
    banner_alias: props.ban?.banner_player?.alias,
    created_at: props.ban?.created_at
        ? new Date(props.ban.created_at)
        : new Date(),
    reason: props.ban?.reason,
})

const isEdit = computed(() => props.ban != null)

function submit() {
    props.submit(form)
}
</script>

<template>
    <form @submit.prevent="submit">
        {{ form.errors }}

        <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
            <div class="sm:col-span-2">
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Player<span class="text-red-500">*</span>
                </label>
                <PlayerPicker
                    v-model:uuid="form.banned_uuid"
                    v-model:alias="form.banned_alias"
                />
                <div v-if="form.errors.banned_uuid" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.banned_uuid }}
                </div>
            </div>
            <div class="sm:col-span-2">
                <label for="category" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Date of Ban<span class="text-red-500">*</span>
                </label>
                <DateTimePicker
                    v-model="form.created_at"
                    @change="form.created_at = $event"
                />
                <div v-if="form.errors.created_at" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.created_at }}
                </div>
            </div>
            <div class="sm:col-span-2">
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Banned By</label>
                <PlayerPicker
                    v-model:uuid="form.banner_uuid"
                    v-model:alias="form.banner_alias"
                />
                <span class="block mt-2 text-xs font-medium text-gray-400 dark:text-white">
                    Leaving this empty will show it as banned by System
                </span>
                <div v-if="form.errors.banner_uuid" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.banner_uuid }}
                </div>
            </div>
            <div class="sm:col-span-2">
                <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Reason for Ban<span class="text-red-500">*</span>
                </label>
                <textarea
                    v-model="form.reason"
                    id="description"
                    rows="3"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="eg. Repeated and intentional griefing of builds"
                ></textarea>
                <span class="block mt-2 text-xs font-medium text-gray-400 dark:text-white">
                    This is the message shown to the player - keep it short and concise.<br />
                    <strong>Do not tell them to appeal</strong>, this is already appended to the end automatically.
                </span>
                <div v-if="form.errors.reason" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.reason }}
                </div>
            </div>
        </div>
        <button
            :disabled="form.processing"
            type="submit"
            class="inline-flex items-center px-5 py-2.5 mt-4 sm:mt-6 text-sm font-medium text-center text-white bg-blue-700 rounded-lg focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900 hover:bg-blue-800"
        >
            {{ isEdit ? 'Update' : 'Create' }}
        </button>

        <span class="block mt-2 text-xs font-medium text-gray-400 dark:text-white">
            Note: The player will be kicked and banned if they are currently on the server
        </span>
    </form>
</template>
