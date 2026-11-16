<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import type { Player } from '../../Data/Player'
import MinecraftAvatar from '../../Components/MinecraftAvatar.vue'
import { format } from '../../Utilities/DateFormatter'
import PlayerBanListTable from './Partials/PlayerBanListTable.vue'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import PlayerWarningListTable from './Partials/PlayerWarningListTable.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import { ref } from 'vue'
import Spinner from '../../Components/Spinner.vue'
import OutlinedButton from '../../Components/OutlinedButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import FilledButton from '../../Components/FilledButton.vue'
import ErrorAlert from '../../Components/ErrorAlert.vue'
import axios from 'axios'
import PlayerIPListTable from './Partials/PlayerIPListTable.vue'
import PlayerHomeListTable from './Partials/PlayerHomeListTable.vue'
import { Icons } from '../../Icons'
import usePermissions from '../../Composables/usePermissions'
import PageHeader from '../../../shared/Components/PageHeader.vue'
import ManageBreadcrumbs from '../../Components/ManageBreadcrumbs.vue'
import LinkButton from '../../../shared/Components/LinkButton.vue'

interface Props {
    player: Player,
    success?: string,
}

const { can } = usePermissions()

const props = defineProps<Props>()
const error = ref<string|null>(null)

const isRefreshingAlias = ref(false)

async function refreshAlias() {
    isRefreshingAlias.value = true

    try {
        const response = await axios.post('/manage/players/' + props.player.id + '/alias/refresh')
        const player = response.data
        router.replace({
            props: (currentProps) => ({
                ...currentProps,
                player: player,
                success: 'Alias successfully refreshed',
            })
        })
    } catch (e) {
        error.value = e.response?.data?.message ?? e.message
    } finally {
        isRefreshingAlias.value = false
    }
}
</script>

<template>
    <div>
        <Head :title="'Viewing Player: ' + (player.alias ?? player.uuid)"/>

        <PageHeader :title="player.alias ?? player.uuid" description="Review Minecraft player details, moderation history, homes, and linked account.">
            <template #breadcrumbs>
                <ManageBreadcrumbs :items="[{ label: 'Manage', href: '/manage' }, { label: 'Minecraft' }, { label: 'Players', href: '/manage/players' }, { label: player.alias ?? player.uuid }]" />
            </template>
            <template #actions v-if="can('web.manage.players.edit')">
                <OutlinedButton
                    variant="secondary"
                    @click="refreshAlias"
                >
                    <Spinner v-if="isRefreshingAlias" />
                    <span v-else class="flex flex-row items-center justify-center gap-2">
                        <SvgIcon :svg="Icons.refresh" />
                        Fetch Alias
                    </span>
                </OutlinedButton>

                <LinkButton :href="'/manage/players/' + player.id + '/edit'" variant="outline">
                    <SvgIcon :svg="Icons.pencil" />
                    Edit Player
                </LinkButton>
            </template>
        </PageHeader>

        <SuccessAlert v-if="success" :message="success" class="mb-4"/>
        <ErrorAlert v-if="error" :errors="[error]" class="mb-4" />

        <div class="mt-4 flex flex-col gap-4 xl:flex-row">
            <section>
                <Card padding="none" class="max-w-2xl">
                    <div class="p-4 flex flex-col items-center">
                        <MinecraftAvatar :uuid="player.uuid" :size="96" class="shadow-lg" />
                        <h2 class="mt-4 text-2xl font-semibold text-foreground">{{ player.alias ?? player.uuid }}</h2>
                        <span class="mt-2 text-sm text-muted">{{ player.uuid }}</span>
                    </div>
                    <div class="p-4 border-t border-border">
                        <div class="space-y-4">
                            <dl class="flex items-center justify-between gap-2">
                                <dt class="text-sm text-muted">Created At</dt>
                                <dd class="text-sm text-foreground">
                                    {{ format(player.created_at) }}
                                </dd>
                            </dl>
                            <dl class="flex items-center justify-between gap-2">
                                <dt class="text-sm text-muted">Updated At</dt>
                                <dd class="text-sm text-foreground">
                                    {{ format(player.updated_at) }}
                                </dd>
                            </dl>
                            <dl class="flex items-center justify-between gap-2">
                                <dt class="text-sm text-muted">Last Connected At</dt>
                                <dd class="text-sm text-foreground">
                                    {{ player.last_connected_at ? format(player.last_connected_at) : 'Never' }}
                                </dd>
                            </dl>
                            <dl class="flex items-center justify-between gap-2">
                                <dt class="text-sm text-muted">Last Seen At</dt>
                                <dd class="text-sm text-foreground">
                                    {{ player.last_seen_at ? format(player.last_seen_at) : 'Never' }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </Card>

                <Card padding="md" class="max-w-2xl mt-4">
                    <h2 class="text-base font-semibold">Meta</h2>

                    <div class="mt-4 space-y-4">
                        <dl class="flex items-center justify-between gap-2">
                            <dt class="text-sm text-muted">Nickname</dt>
                            <dd class="text-sm text-foreground">
                                <span v-if="player.nickname">{{ player.nickname }}</span>
                                <span v-else class="italic text-muted">Not set</span>
                            </dd>
                        </dl>
                    </div>
                </Card>

                <Card padding="md" class="max-w-2xl mt-4">
                    <h2 class="text-base font-semibold">Owner</h2>

                    <div class="mt-4">
                        <div v-if="player.account_id" class="flex items-center gap-2">
                            <SvgIcon :svg="Icons.link" class="size-4" aria-hidden="true" />

                            <Link
                                :href="'/manage/accounts/' + player.account_id"
                                class="font-medium text-primary-active underline-offset-2 hover:underline"
                            >
                                {{ player.account?.username }}
                            </Link>
                        </div>
                        <div v-else class="flex items-center gap-2 text-muted">
                            <SvgIcon :svg="Icons.unlink" class="size-4" aria-hidden="true" />
                            No Linked Account
                        </div>
                    </div>
                </Card>
            </section>

            <section class="grow">
                <Card padding="none" v-if="can('web.manage.uuid_bans.view')">
                    <div class="p-4 flex justify-between items-center">
                        <h2 class="text-base font-semibold">Player Bans</h2>
                        <LinkButton :href="'/manage/player-bans/create?uuid=' + player.uuid" variant="danger">Create</LinkButton>
                    </div>
                    <InfinitePagination
                        :path="'/manage/players/' + player.id + '/bans'"
                        v-slot="source"
                        class="overflow-x-auto"
                    >
                        <PlayerBanListTable :bans="source.data"/>
                    </InfinitePagination>
                </Card>

                <Card padding="none" class="mt-4" v-if="can('web.manage.warnings.view')">
                    <div class="p-4 flex justify-between items-center">
                        <h2 class="text-base font-semibold">Player Warnings</h2>
                        <LinkButton :href="'/manage/warnings/create?uuid=' + player.uuid" variant="danger">Create</LinkButton>
                    </div>
                    <InfinitePagination
                        :path="'/manage/players/' + player.id + '/warnings'"
                        v-slot="source"
                        class="overflow-x-auto"
                    >
                        <PlayerWarningListTable :warnings="source.data"/>
                    </InfinitePagination>
                </Card>

                <Card padding="none" class="mt-4" v-if="can('web.manage.homes.view')">
                    <div class="p-4 flex justify-between items-center">
                        <h2 class="text-base font-semibold">Homes</h2>
                    </div>
                    <InfinitePagination
                        :path="'/manage/players/' + player.id + '/homes'"
                        v-slot="source"
                        class="overflow-x-auto"
                    >
                        <PlayerHomeListTable :homes="source.data"/>
                    </InfinitePagination>
                </Card>

                <Card padding="none" class="mt-4" v-if="can('web.manage.players.view_ips')">
                    <div class="p-4 flex justify-between items-center">
                        <h2 class="text-base font-semibold">Known IP Addresses</h2>
                    </div>
                    <InfinitePagination
                        :path="'/manage/players/' + player.id + '/ips'"
                        v-slot="source"
                        class="overflow-x-auto"
                    >
                        <PlayerIPListTable :ips="source.data"/>
                    </InfinitePagination>
                </Card>
            </section>
        </div>
    </div>
</template>
