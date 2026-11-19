<script setup lang="ts">
import { BanAppealStatus } from '../../../../../generated/openapi-enums'
import { Link } from '@inertiajs/vue3'
import MinecraftAvatar from '../../../../manage/Components/MinecraftAvatar.vue'
import Badge from '../../../../shared/Components/Badge.vue'
import { distance, distanceFromNow, format } from '../../../../manage/Utilities/DateFormatter'

const statusLabels: Record<App.Domains.BanAppeals.Data.BanAppealStatus, string> = {
    [BanAppealStatus.pending]: 'Awaiting review',
    [BanAppealStatus.acceptedUnban]: 'Unbanned',
    [BanAppealStatus.acceptedTempban]: 'Temp banned',
    [BanAppealStatus.denied]: 'Denied',
}

const statusVariants: Record<App.Domains.BanAppeals.Data.BanAppealStatus, 'warning' | 'success' | 'info' | 'danger'> = {
    [BanAppealStatus.pending]: 'warning',
    [BanAppealStatus.acceptedUnban]: 'success',
    [BanAppealStatus.acceptedTempban]: 'info',
    [BanAppealStatus.denied]: 'danger',
}

interface Props {
    banAppeals: App.Http.Data.AdminBanAppealData[],
}
defineProps<Props>()
</script>

<template>
    <ul v-if="banAppeals.length > 0" class="space-y-3">
        <li v-for="appeal in banAppeals" :key="appeal.id">
            <Link
                :href="`/review/ban-appeals/${appeal.id}`"
                class="group block rounded-xl border border-border bg-surface p-4 transition-colors hover:border-border-strong hover:bg-surface-hover focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus focus-visible:ring-offset-2 focus-visible:ring-offset-surface motion-reduce:transition-none"
            >
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex min-w-0 items-center gap-3">
                        <MinecraftAvatar
                            v-if="appeal.game_player_ban"
                            :uuid="appeal.game_player_ban.banned_player.uuid"
                            :size="40"
                            class="shrink-0 overflow-hidden rounded-full"
                        />
                        <div
                            v-else
                            class="flex size-10 shrink-0 items-center justify-center rounded-full bg-surface-muted text-sm font-semibold text-muted"
                            aria-hidden="true"
                        >
                                    {{ appeal.email?.charAt(0).toUpperCase() ?? '?' }}
                        </div>

                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="truncate text-base font-semibold text-foreground">
                                    {{ appeal.game_player_ban?.banned_player.alias ?? appeal.email }}
                                </h2>
                                <Badge
                                    :variant="appeal.status === null ? 'warning' : statusVariants[appeal.status]"
                                    size="sm"
                                >
                                    {{ appeal.status === null ? 'Unknown' : statusLabels[appeal.status] }}
                                </Badge>
                            </div>
                            <p class="mt-1 text-xs text-muted">
                                Ban appeal #{{ appeal.id }}
                                <span v-if="appeal.game_player_ban">
                                    <span aria-hidden="true">·</span>
                                    {{ appeal.game_player_ban.banned_player.alias }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="flex shrink-0 flex-wrap items-center gap-x-3 gap-y-1 text-xs text-muted sm:justify-end">
                        <span>{{ format(appeal.created_at) }}</span>
                        <span>
                            {{ appeal.decided_at
                                ? `Reviewed in ${distance(appeal.created_at, appeal.decided_at)}`
                                : `Waiting ${distanceFromNow(appeal.created_at)}` }}
                        </span>
                    </div>
                </div>

                <div class="mt-4 rounded-none border border-border bg-surface-muted p-3">
                    <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-subtle">Appeal message</p>
                    <p class="line-clamp-2 whitespace-pre-wrap text-sm leading-6 text-foreground">
                        {{ appeal.unban_reason }}
                    </p>
                </div>

                <div class="mt-3 flex flex-wrap items-center justify-between gap-2">
                    <p class="truncate text-xs text-muted">
                        {{ appeal.game_player_ban?.reason ? `Ban reason: ${appeal.game_player_ban.reason}` : appeal.email }}
                    </p>
                    <span class="text-sm font-semibold text-primary-active group-hover:underline">
                        {{ appeal.status === BanAppealStatus.pending ? 'Review appeal' : 'View appeal' }}
                    </span>
                </div>
            </Link>
        </li>
    </ul>

    <div v-else class="rounded-xl border border-dashed border-border-strong bg-surface p-8 text-center">
        <h2 class="text-base font-semibold text-foreground">No ban appeals to review</h2>
        <p class="mt-1 text-sm text-muted">New player requests will appear here.</p>
    </div>
</template>
