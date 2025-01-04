<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import BackButton from '../../Components/BackButton.vue'
import type { Player } from '../../Data/Player'
import MinecraftAvatar from '../../Components/MinecraftAvatar.vue'
import { format } from '../../Utilities/DateFormatter'
import PlayerBanListTable from './Partials/PlayerBanListTable.vue'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import PlayerWarningListTable from './Partials/PlayerWarningListTable.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import { ref } from 'vue'
import Spinner from '../../Components/Spinner.vue'
import ToolBar from '../../Components/ToolBar.vue'
import OutlinedButton from '../../Components/OutlinedButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import FilledButton from '../../Components/FilledButton.vue'

interface Props {
    player: Player,
    success?: string,
}

const props = defineProps<Props>()

const isRefreshingAlias = ref(false)

async function refreshAlias() {
    isRefreshingAlias.value = true

    router.post('/manage/players/' + props.player.player_minecraft_id + '/alias/refresh', null, {
        preserveScroll: true,
        onFinish: () => isRefreshingAlias.value = false,
    })
}
</script>

<template>
    <div class="p-3 sm:p-5 mx-auto max-w-screen-xl">
        <Head :title="'Viewing Player: ' + player.alias ?? player.uuid"/>

        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <ToolBar>
            <template v-slot:left>
                <BackButton href="/manage/players"/>
            </template>
            <template v-slot:right>
                <OutlinedButton
                    variant="secondary"
                    @click="refreshAlias"
                >
                    <Spinner v-if="isRefreshingAlias" />
                    <span v-else class="flex flex-row items-center justify-center gap-2">
                        <SvgIcon icon="refresh" />
                        Fetch Alias
                    </span>
                </OutlinedButton>

                <Link :href="'/manage/players/' + player.player_minecraft_id + '/edit'">
                    <FilledButton variant="primary">
                        <SvgIcon icon="pencil" />
                        Edit Player
                    </FilledButton>
                </Link>
            </template>
        </ToolBar>

        <div class="mt-4 flex flex-col gap-4 md:flex-row">
            <section>
                <Card class="max-w-2xl">
                    <div class="p-4 flex flex-col items-center">
                        <MinecraftAvatar :uuid="player.uuid" :size="96" class="shadow-lg" />
                        <h1 class="mt-4 text-2xl font-bold text-gray-900 dark:text-white">{{ player.alias }}</h1>
                        <span class="mt-2 text-sm text-gray-500">{{ player.uuid }}</span>
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

                <Card class="max-w-2xl mt-4 p-4">
                    <h2 class="font-bold">Owner</h2>

                    <div class="mt-4">
                        <div v-if="player.account_id" class="flex justify-between items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                            </svg>

                            <Link
                                :href="'/manage/accounts/' + player.account_id"
                                class="text-blue-500"
                            >
                                {{ player.account?.username }}
                            </Link>
                        </div>
                        <div v-else class="flex items-center gap-2 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.181 8.68a4.503 4.503 0 0 1 1.903 6.405m-9.768-2.782L3.56 14.06a4.5 4.5 0 0 0 6.364 6.365l3.129-3.129m5.614-5.615 1.757-1.757a4.5 4.5 0 0 0-6.364-6.365l-4.5 4.5c-.258.26-.479.541-.661.84m1.903 6.405a4.495 4.495 0 0 1-1.242-.88 4.483 4.483 0 0 1-1.062-1.683m6.587 2.345 5.907 5.907m-5.907-5.907L8.898 8.898M2.991 2.99 8.898 8.9" />
                            </svg>
                            No Linked Account
                        </div>
                    </div>
                </Card>
            </section>

            <section class="grow">
                <Card>
                    <div class="p-4 flex justify-between items-center">
                        <h2 class="font-bold">Player Bans</h2>
                        <Link :href="'/manage/player-bans/create?uuid=' + player.uuid">
                            <FilledButton variant="secondary">Create</FilledButton>
                        </Link>
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
                    <div class="p-4 flex justify-between items-center">
                        <h2 class="font-bold">Player Warnings</h2>
                        <Link :href="'/manage/warnings/create?uuid=' + player.uuid">
                            <FilledButton variant="secondary">Create</FilledButton>
                        </Link>
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
