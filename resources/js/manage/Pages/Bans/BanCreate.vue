<script setup>
import { Head, router } from '@inertiajs/vue3'
import { computed, reactive } from 'vue'
import DateTimePicker from '../../Components/DateTimePicker.vue'
import PlayerPicker from '../../Components/PlayerPicker.vue'
import Card from '../../Components/Card.vue'

const props = defineProps({
    account: Object,
})

const form = reactive({
    banned_uuid: null,
    banner_uuid: null,
    created_at: null,
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
    router.post('/manage/player-bans', form)
}
</script>

<template>
    <Head title="Create a Ban" />

    <h1 class="my-4 mx-8 text-3xl text-gray-600">
        Manage Bans
    </h1>

    <section class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5">
        <div class="mx-auto max-w-screen-xl">

            <Card>
                <div class="p-8 max-w-2xl">
                    <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Create a Ban</h2>
                    <form @submit.prevent="submit">
                        <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
                            <div class="sm:col-span-2">
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Player</label>
                                <PlayerPicker
                                    @uuid-change="form.banned_uuid = $event"
                                />
                            </div>
                            <div class="sm:col-span-2">
                                <label for="category" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date of Ban</label>
                                <DateTimePicker />
                            </div>
                            <div class="sm:col-span-2">
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Banned By</label>
                                <PlayerPicker
                                    :initial-player="bannedBy"
                                    @uuid-change="form.banner_uuid = $event"
                                />
                            </div>
                            <div class="sm:col-span-2">
                                <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Reason for Ban</label>
                                <textarea
                                    v-model="form.reason"
                                    id="description"
                                    rows="3"
                                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="eg. Repeated and intentional griefing of builds"
                                ></textarea>
                                <span class="block mt-2 text-sm font-medium text-gray-400 dark:text-white">
                                    This is the message shown to the player - keep it short and concise.<br />
                                    <strong>Do not tell them to appeal</strong>, this is already appended to the end automatically.
                                </span>
                            </div>
                        </div>
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 mt-4 sm:mt-6 text-sm font-medium text-center text-white bg-blue-700 rounded-lg focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900 hover:bg-blue-800">
                            Create
                        </button>

                        <span class="block mt-2 text-sm font-medium text-gray-400 dark:text-white">
                            Note: The player will be kicked and banned if they are currently on the server
                        </span>
                    </form>
                </div>
            </Card>

        </div>
    </section>
</template>
