<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import BackButton from '../../Components/BackButton.vue'
import type { Player } from '../../Data/Player'
import MinecraftAvatar from '../../Components/MinecraftAvatar.vue'
import { format } from '../../Utilities/DateFormatter'
import PlayerBanListTable from './Partials/PlayerBanListTable.vue'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import GroupMemberListTable from '../Groups/Partials/GroupMemberListTable.vue'
import PlayerWarningListTable from './Partials/PlayerWarningListTable.vue'

interface Props {
    player: Player,
}

defineProps<Props>()
</script>

<template>
    <div class="p-3 sm:p-5 mx-auto max-w-screen-xl">
        <Head :title="'Viewing Player: ' + player.alias ?? player.uuid"/>

        <section>
            <Card class="mb-4">
                <div class="flex flex-row items-center justify-between p-4">
                    <BackButton href="/manage/players"/>

                    <Link
                        :href="'/manage/players/' + player.player_minecraft_id + '/edit'"
                        as="button"
                        class="
                                flex flex-row items-center justify-center gap-2 px-4 py-2 rounded-lg
                                text-sm text-white bg-blue-700
                                hover:bg-primary-800
                                focus:ring-4 focus:ring-blue-300
                                dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800
                            "
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"/>
                        </svg>
                        Edit Player
                    </Link>
                </div>
            </Card>
        </section>

        <div class="flex flex-col gap-4 md:flex-row">
            <section>
                <Card class="max-w-2xl">
                    <div class="p-4 flex flex-col items-center">
                        <MinecraftAvatar :alias="player.alias ?? player.uuid" :size="96" class="shadow-lg" />
                        <h1 class="mt-4 text-2xl font-bold text-gray-900 dark:text-white">{{ player.alias }}</h1>
                        <span class="mt-2 text-sm text-gray-500">{{ player.uuid }}</span>
                    </div>
                    <div class="p-4 border-t border-gray-200">
                        <div class="space-y-4">
                            <dl class="flex items-center justify-between gap-2">
                                <dt class="text-gray-500 dark:text-gray-400">Owner</dt>
                                <dd class="text-gray-900 dark:text-white">
                                    <Link
                                        v-if="player.account_id"
                                        :href="'/manage/accounts/' + player.account_id"
                                        class="text-blue-500"
                                    >
                                        {{ player.account?.username }}
                                    </Link>
                                    <span v-else class="text-gray-400">No Linked Account</span>
                                </dd>
                            </dl>
                        </div>
                    </div>
                    <div class="p-4 border-t border-gray-200">
                        <div class="space-y-4">
                            <dl class="flex items-center justify-between gap-2">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Created At</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">
                                    {{ format(player.created_at) }}
                                </dd>
                            </dl>
                            <dl class="flex items-center justify-between gap-2">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Updated At</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">
                                    {{ format(player.updated_at) }}
                                </dd>
                            </dl>
                            <dl class="flex items-center justify-between gap-2">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Last Synced At</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">
                                    {{ player.last_synced_at ? format(player.last_synced_at) : 'Never' }}
                                </dd>
                            </dl>
                            <dl class="flex items-center justify-between gap-2">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Last Seen At</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">
                                    {{ player.last_seen_at ? format(player.last_seen_at) : 'Never' }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </Card>
            </section>

            <section class="grow">
                <Card>
                    <div class="p-4 font-bold">
                        Player Bans
                    </div>
                    <InfinitePagination
                        :path="'/manage/players/' + player.player_minecraft_id + '/bans'"
                        v-slot="source"
                        class="overflow-x-auto"
                    >
                        <PlayerBanListTable :bans="source.data"/>
                    </InfinitePagination>
                </Card>

                <Card class="mt-4">
                    <div class="p-4 font-bold">
                        Player Warnings
                    </div>
                    <InfinitePagination
                        :path="'/manage/players/' + player.player_minecraft_id + '/warnings'"
                        v-slot="source"
                        class="overflow-x-auto"
                    >
                        <PlayerWarningListTable :warnings="source.data"/>
                    </InfinitePagination>
                </Card>
            </section>
        </div>
    </div>
</template>
