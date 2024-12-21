<script setup>
import { Head, router, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import DateTimePicker from '../../Components/DateTimePicker.vue'
import PlayerPicker from '../../Components/PlayerPicker.vue'
import Card from '../../Components/Card.vue'

const props = defineProps({
    account: Object,
})

const form = useForm({
    banned_uuid: null,
    banner_uuid: null,
    // created_at: null,
    reason: null,
})

const bannedBy = computed(() => {
    const player = props.account.minecraft_account[0]
    if (!player) return null

    return {
        uuid: player.uuid,
        alias: player.alias,
    }
})

function submit() {
    form.post('/manage/player-bans')
}
</script>

<template>
    <section class="p-3 sm:p-5">
        <Head title="Create a Ban" />

        <div class="mx-auto max-w-screen-xl pb-8 px-8">
            <h1 class="text-3xl text-gray-600">
                Manage Bans
            </h1>
        </div>

        <div class="mx-auto max-w-screen-xl">
            <Card>
                <div class="p-8 max-w-2xl">
                    <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Create a Ban</h2>
                    <div class="text-sm text-gray-500">Prevent a Minecraft UUID from connecting to our server</div>
                    <hr class="my-6" />

                    <form @submit.prevent="submit">
                        <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
                            <div class="sm:col-span-2">
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Player<span class="text-red-500">*</span>
                                </label>
                                <PlayerPicker
                                    @uuid-change="form.banned_uuid = $event"
                                />
                                <div v-if="form.errors.banned_uuid" class="text-xs text-red-500 font-bold mt-2">
                                    {{ form.errors.banned_uuid }}
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="category" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Date of Ban<span class="text-red-500">*</span>
                                </label>
                                <DateTimePicker />
                                <div v-if="form.errors.created_at" class="text-xs text-red-500 font-bold mt-2">
                                    {{ form.errors.created_at }}
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Banned By</label>
                                <PlayerPicker
                                    :initial-player="bannedBy"
                                    @uuid-change="form.banner_uuid = $event"
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
                            Create
                        </button>

                        <span class="block mt-2 text-xs font-medium text-gray-400 dark:text-white">
                            Note: The player will be kicked and banned if they are currently on the server
                        </span>
                    </form>
                </div>
            </Card>

        </div>
    </section>
</template>
