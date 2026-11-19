<script setup lang="ts">
import { Routes } from '../../../../Navigation/Routes'
import { Head, Link, router } from '@inertiajs/vue3'
import Card from '../../../../Components/ManageCard.vue'
import MinecraftAvatar from '../../../../Components/MinecraftAvatar.vue'
import { format, timeAgo } from '../../../../Utilities/DateFormatter'
import PlayerBanListTable from './Partials/PlayerBanListTable.vue'
import InfinitePagination from '../../../../Components/InfinitePagination.vue'
import PlayerWarningListTable from './Partials/PlayerWarningListTable.vue'
import SuccessAlert from '../../../../Components/SuccessAlert.vue'
import { computed, ref } from 'vue'
import Spinner from '../../../../Components/Spinner.vue'
import OutlinedButton from '../../../../Components/OutlinedButton.vue'
import Pill from '../../../../Components/Pill.vue'
import SvgIcon from '../../../../Components/SvgIcon.vue'
import ErrorAlert from '../../../../Components/ErrorAlert.vue'
import axios from 'axios'
import PlayerIPListTable from './Partials/PlayerIPListTable.vue'
import PlayerHomeListTable from './Partials/PlayerHomeListTable.vue'
import { Icons } from '../../../../Navigation/Icons'
import usePermissions from '../../../../Composables/usePermissions'
import PageHeader from '../../../../Components/PageHeader.vue'
import LinkButton from '../../../../Components/LinkButton.vue'
import CopyLocationButton from '../../../../Components/CopyLocationButton.vue'

const { can } = usePermissions()

const props = defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/Players/PlayerShow']>()
const error = ref<string|null>(null)

const isRefreshingAlias = ref(false)
const hasLastLocation = computed(() => {
    const coordinates = [
        props.player.last_location_x,
        props.player.last_location_y,
        props.player.last_location_z,
        props.player.last_location_yaw,
        props.player.last_location_pitch,
    ]

    return coordinates.every((coordinate) => typeof coordinate === 'number' && Number.isFinite(coordinate))
})

async function refreshAlias() {
    isRefreshingAlias.value = true

    try {
        const response = await axios.post<App.Http.Data.MinecraftPlayerData>(
            '/manage/players/' + props.player.id + '/alias/refresh',
        )
        const player = response.data
        router.replace({
            props: (currentProps) => ({
                ...currentProps,
                player: player,
                success: 'Alias successfully refreshed',
            })
        })
    } catch (e) {
        error.value = axios.isAxiosError<{ message?: string }>(e)
            ? e.response?.data?.message ?? e.message
            : e instanceof Error ? e.message : 'Unable to refresh the player alias.'
    } finally {
        isRefreshingAlias.value = false
    }
}
</script>

<template>
    <div class="space-y-6">
        <Head :title="'Viewing Player: ' + (player.alias ?? player.uuid)"/>

        <PageHeader title="Player Details">
            <template #actions v-if="can('web.manage.players.edit')">
                <OutlinedButton
                    variant="secondary"
                    @click="refreshAlias"
                >
                    <Spinner v-if="isRefreshingAlias" />
                    <span v-else class="flex flex-row items-center justify-center gap-2">
                        <SvgIcon :svg="Icons.refresh" />
                        <span v-if="player.alias">Refetch Alias</span>
                        <span v-else>Fetch Alias</span>
                    </span>
                </OutlinedButton>

                <LinkButton :href="Routes.manage.players.edit(player.id)" variant="outline">
                    <SvgIcon :svg="Icons.pencil" />
                    Edit Player
                </LinkButton>
            </template>
        </PageHeader>

        <SuccessAlert v-if="success" :message="success" />
        <ErrorAlert v-if="error" :errors="[error]" />

        <Card padding="none">
            <div class="flex flex-col gap-5 p-5 sm:flex-row sm:items-center sm:gap-6 sm:p-6">
                <MinecraftAvatar :uuid="player.uuid" :size="88" class="shrink-0 rounded-lg shadow-md" />
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wide text-muted">Minecraft player</p>
                    <h2 class="mt-1 truncate text-xl font-semibold text-foreground sm:text-2xl">
                        {{ player.alias ?? "-" }}  <span class="font-normal text-muted">(#{{ player.id }})</span>
                    </h2>
                    <p class="mt-1 break-all font-mono text-sm text-muted">{{ player.uuid }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 border-t border-border p-5 sm:grid-cols-2 sm:gap-6 sm:p-6 xl:grid-cols-4">
                <div>
                    <p class="text-xs text-muted">Created</p>
                    <p class="mt-1 text-sm font-medium text-foreground">{{ format(player.created_at) }}</p>
                    <p class="mt-0.5 text-xs text-muted">{{ timeAgo(player.created_at) }}</p>
                </div>
                <div>
                    <p class="text-xs text-muted">Last updated</p>
                    <p class="mt-1 text-sm font-medium text-foreground">{{ format(player.updated_at) }}</p>
                    <p class="mt-0.5 text-xs text-muted">{{ timeAgo(player.updated_at) }}</p>
                </div>
                <div>
                    <p class="text-xs text-muted">Last connected</p>
                    <p class="mt-1 text-sm font-medium text-foreground">
                        {{ player.last_connected_at ? format(player.last_connected_at) : 'Never' }}
                    </p>
                    <p class="mt-0.5 text-xs text-muted">{{ timeAgo(player.last_connected_at) }}</p>
                </div>
                <div>
                    <p class="text-xs text-muted">Last seen</p>
                    <p class="mt-1 text-sm font-medium text-foreground">
                        {{ player.last_seen_at ? format(player.last_seen_at) : 'Never' }}
                    </p>
                    <p class="mt-0.5 text-xs text-muted">{{ timeAgo(player.last_seen_at) }}</p>
                </div>
            </div>
        </Card>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.7fr)_minmax(18rem,0.8fr)] xl:items-start">
            <section class="order-2 space-y-4 xl:order-1">
                <Card v-if="can('web.manage.uuid_bans.view')" padding="none">
                    <div class="flex items-center justify-between gap-4 p-4 sm:px-5">
                        <div>
                            <h2 class="text-base font-semibold text-foreground">Player bans</h2>
                            <p class="mt-0.5 text-sm text-muted">Restrictions associated with this player.</p>
                        </div>
                        <LinkButton :href="Routes.manage.playerBans.createForPlayer(player.uuid)" variant="outline">
                            <SvgIcon :svg="Icons.plus" class="size-4" aria-hidden="true" />
                            Create
                        </LinkButton>
                    </div>
                    <!-- @vue-generic {App.Http.Data.AdminGamePlayerBanData} -->
                    <InfinitePagination
                        :path="'/manage/players/' + player.id + '/bans'"
                        v-slot="source"
                        class="overflow-x-auto"
                    >
                        <PlayerBanListTable :bans="source.data"/>
                    </InfinitePagination>
                </Card>

                <Card v-if="can('web.manage.warnings.view')" padding="none">
                    <div class="flex items-center justify-between gap-4 p-4 sm:px-5">
                        <div>
                            <h2 class="text-base font-semibold text-foreground">Player warnings</h2>
                            <p class="mt-0.5 text-sm text-muted">Moderation notes recorded for this player.</p>
                        </div>
                        <LinkButton :href="Routes.manage.warnings.createForPlayer(player.uuid)" variant="outline">
                            <SvgIcon :svg="Icons.plus" class="size-4" aria-hidden="true" />
                            Create
                        </LinkButton>
                    </div>
                    <!-- @vue-generic {App.Http.Data.AdminPlayerWarningData} -->
                    <InfinitePagination
                        :path="'/manage/players/' + player.id + '/warnings'"
                        v-slot="source"
                        class="overflow-x-auto"
                    >
                        <PlayerWarningListTable :warnings="source.data"/>
                    </InfinitePagination>
                </Card>

                <Card v-if="can('web.manage.homes.view')" padding="none">
                    <div class="p-4 sm:px-5">
                        <h2 class="text-base font-semibold text-foreground">Homes</h2>
                        <p class="mt-0.5 text-sm text-muted">Saved locations belonging to this player.</p>
                    </div>
                    <!-- @vue-generic {App.Http.Data.MinecraftHomeData} -->
                    <InfinitePagination
                        :path="'/manage/players/' + player.id + '/homes'"
                        v-slot="source"
                        class="overflow-x-auto"
                    >
                        <PlayerHomeListTable :homes="source.data" />
                    </InfinitePagination>
                </Card>

                <Card v-if="can('web.manage.players.view_ips')" padding="none">
                    <div class="p-4 sm:px-5">
                        <h2 class="text-base font-semibold text-foreground">Known IP addresses</h2>
                        <p class="mt-0.5 text-sm text-muted">Network addresses seen for this player.</p>
                    </div>
                    <!-- @vue-generic {App.Http.Data.MinecraftPlayerIpData} -->
                    <InfinitePagination
                        :path="'/manage/players/' + player.id + '/ips'"
                        v-slot="source"
                        class="overflow-x-auto"
                    >
                        <PlayerIPListTable :ips="source.data"/>
                    </InfinitePagination>
                </Card>
            </section>

            <aside class="order-1 space-y-4 xl:order-2">
                <Card padding="none">
                    <header class="border-b border-border p-4">
                        <h2 class="text-base font-semibold text-foreground">Player details</h2>
                        <p class="mt-1 text-sm text-muted">Profile, activity, and location</p>
                    </header>

                    <section class="space-y-3 p-4">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-muted">Identity</h3>

                        <Link
                            v-if="player.account_id"
                            :href="Routes.manage.accounts.show(player.account_id)"
                            class="group flex items-center justify-between gap-3 rounded-md border border-border p-3 transition-colors hover:border-primary"
                        >
                            <span class="flex min-w-0 items-center gap-3">
                                <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-info-soft text-info">
                                    <SvgIcon :svg="Icons.link" class="size-4" aria-hidden="true" />
                                </span>
                                <span class="min-w-0">
                                    <span class="block text-xs text-muted">Linked account</span>
                                    <span class="block truncate text-sm font-medium text-primary-active group-hover:underline">
                                        {{ player.account?.username ?? 'Account #' + player.account_id }}
                                    </span>
                                </span>
                            </span>
                            <SvgIcon :svg="Icons.chevronRight" class="size-4 shrink-0 text-muted" aria-hidden="true" />
                        </Link>
                        <div v-else class="flex items-center gap-3 rounded-md border border-dashed border-border p-3">
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-surface-muted text-muted">
                                <SvgIcon :svg="Icons.unlink" class="size-4" aria-hidden="true" />
                            </span>
                            <span>
                                <span class="block text-xs text-muted">Linked account</span>
                                <span class="block text-sm text-foreground">No account linked</span>
                            </span>
                        </div>

                        <dl class="grid gap-3">
                            <div class="rounded-md border border-border p-3">
                                <dt class="text-xs text-muted">Nickname</dt>
                                <dd class="mt-1 text-sm font-medium text-foreground">
                                    <span v-if="player.nickname">{{ player.nickname }}</span>
                                    <span v-else class="font-normal italic text-muted">Not set</span>
                                </dd>
                            </div>
                        </dl>
                    </section>

                    <section class="space-y-3 border-t border-border p-4">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-muted">Activity</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <dl class="rounded-md border border-border p-3">
                                <dt class="text-xs text-muted">Chat status</dt>
                                <dd class="mt-2">
                                    <Pill :variant="player.muted ? 'warning' : 'success'" size="sm">
                                        {{ player.muted ? 'Muted' : 'Not muted' }}
                                    </Pill>
                                </dd>
                            </dl>
                            <dl class="rounded-md border border-border p-3">
                                <dt class="text-xs text-muted">Play time</dt>
                                <dd class="mt-1 text-sm font-semibold tabular-nums text-foreground">{{ player.play_time }}</dd>
                            </dl>
                            <dl class="rounded-md border border-border p-3">
                                <dt class="text-xs text-muted">AFK time</dt>
                                <dd class="mt-1 text-sm font-semibold tabular-nums text-foreground">{{ player.afk_time }}</dd>
                            </dl>
                        </div>
                    </section>

                    <section class="space-y-3 border-t border-border p-4">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-muted">Block activity</h3>
                        <div class="grid grid-cols-3 gap-2">
                            <dl class="rounded-md border border-border p-2.5">
                                <dt class="text-xs leading-tight text-muted">Placed</dt>
                                <dd class="mt-1 text-sm font-semibold tabular-nums text-foreground">{{ player.blocks_placed }}</dd>
                            </dl>
                            <dl class="rounded-md border border-border p-2.5">
                                <dt class="text-xs leading-tight text-muted">Destroyed</dt>
                                <dd class="mt-1 text-sm font-semibold tabular-nums text-foreground">{{ player.blocks_destroyed }}</dd>
                            </dl>
                            <dl class="rounded-md border border-border p-2.5">
                                <dt class="text-xs leading-tight text-muted">Travelled</dt>
                                <dd class="mt-1 text-sm font-semibold tabular-nums text-foreground">{{ player.blocks_travelled }}</dd>
                            </dl>
                        </div>
                    </section>

                    <section class="space-y-3 border-t border-border p-4">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-muted">Movement</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <dl class="rounded-md border border-border p-3">
                                <dt class="text-xs text-muted">Walk speed</dt>
                                <dd class="mt-1 text-sm font-medium tabular-nums text-foreground">{{ player.walk_speed }}</dd>
                            </dl>
                            <dl class="rounded-md border border-border p-3">
                                <dt class="text-xs text-muted">Fly speed</dt>
                                <dd class="mt-1 text-sm font-medium tabular-nums text-foreground">{{ player.fly_speed }}</dd>
                            </dl>
                        </div>
                    </section>

                    <section class="space-y-3 border-t border-border p-4">
                        <div class="flex items-center justify-between gap-3">
                            <h3 class="text-xs font-semibold uppercase tracking-wide text-muted">Last location</h3>
                            <CopyLocationButton
                                :x="player.last_location_x"
                                :y="player.last_location_y"
                                :z="player.last_location_z"
                                :yaw="player.last_location_yaw"
                                :pitch="player.last_location_pitch"
                            />
                        </div>
                        <template v-if="hasLastLocation">
                            <dl class="rounded-md border border-border p-3">
                                <dt class="text-xs text-muted">World UUID</dt>
                                <dd class="mt-1 break-all font-mono text-xs text-foreground">
                                    {{ player.last_location_world_uuid ?? 'Not recorded' }}
                                </dd>
                            </dl>
                            <div class="grid grid-cols-3 gap-2">
                                <dl class="rounded-md border border-border p-2.5">
                                    <dt class="text-xs text-muted">X</dt>
                                    <dd class="mt-1 text-sm font-medium tabular-nums text-foreground">{{ player.last_location_x }}</dd>
                                </dl>
                                <dl class="rounded-md border border-border p-2.5">
                                    <dt class="text-xs text-muted">Y</dt>
                                    <dd class="mt-1 text-sm font-medium tabular-nums text-foreground">{{ player.last_location_y }}</dd>
                                </dl>
                                <dl class="rounded-md border border-border p-2.5">
                                    <dt class="text-xs text-muted">Z</dt>
                                    <dd class="mt-1 text-sm font-medium tabular-nums text-foreground">{{ player.last_location_z }}</dd>
                                </dl>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <dl class="rounded-md border border-border p-3">
                                    <dt class="text-xs text-muted">Pitch</dt>
                                    <dd class="mt-1 text-sm font-medium tabular-nums text-foreground">{{ player.last_location_pitch }}</dd>
                                </dl>
                                <dl class="rounded-md border border-border p-3">
                                    <dt class="text-xs text-muted">Yaw</dt>
                                    <dd class="mt-1 text-sm font-medium tabular-nums text-foreground">{{ player.last_location_yaw }}</dd>
                                </dl>
                            </div>
                        </template>
                        <p v-else class="rounded-md border border-dashed border-border p-3 text-sm text-muted">
                            No location has been recorded for this player yet.
                        </p>
                    </section>
                </Card>
            </aside>
        </div>
    </div>
</template>
