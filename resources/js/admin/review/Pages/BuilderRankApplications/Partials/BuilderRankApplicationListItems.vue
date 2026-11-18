<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { BuilderRankApplicationStatus } from '../../../../manage/Data/BuilderRankApplication'
import MinecraftAvatar from '../../../../manage/Components/MinecraftAvatar.vue'
import Badge from '../../../../shared/Components/Badge.vue'
import { distance, distanceFromNow, format } from '../../../../manage/Utilities/DateFormatter'

const statusLabels: Record<App.Domains.BuilderRankApplications.Data.ApplicationStatus, string> = {
    [BuilderRankApplicationStatus.pending]: 'Awaiting review',
    [BuilderRankApplicationStatus.approved]: 'Approved',
    [BuilderRankApplicationStatus.denied]: 'Denied',
}

const statusVariants: Record<App.Domains.BuilderRankApplications.Data.ApplicationStatus, 'warning' | 'success' | 'danger'> = {
    [BuilderRankApplicationStatus.pending]: 'warning',
    [BuilderRankApplicationStatus.approved]: 'success',
    [BuilderRankApplicationStatus.denied]: 'danger',
}

interface Props {
    applications: App.Http.Data.AdminBuilderRankApplicationData[],
}
defineProps<Props>()
</script>

<template>
    <ul v-if="applications.length > 0" class="space-y-3">
        <li v-for="application in applications" :key="application.id">
            <Link
                :href="`/review/builder-ranks/${application.id}`"
                class="group block rounded-xl border border-border bg-surface p-4 transition-colors hover:border-border-strong hover:bg-surface-hover focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus focus-visible:ring-offset-2 focus-visible:ring-offset-surface motion-reduce:transition-none"
            >
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex min-w-0 items-center gap-3">
                        <MinecraftAvatar
                            v-if="application.account?.minecraft_account?.[0]"
                            :uuid="application.account.minecraft_account[0].uuid"
                            :size="40"
                            class="shrink-0 overflow-hidden rounded-full"
                        />
                        <div
                            v-else
                            class="flex size-10 shrink-0 items-center justify-center rounded-full bg-surface-muted text-sm font-semibold text-muted"
                            aria-hidden="true"
                        >
                            {{ application.account?.username?.charAt(0).toUpperCase() ?? '?' }}
                        </div>

                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="truncate text-base font-semibold text-foreground">
                                    {{ application.account?.username ?? 'Unknown account' }}
                                </h2>
                                <Badge
                                    :variant="statusVariants[application.status]"
                                    size="sm"
                                >
                                    {{ statusLabels[application.status] }}
                                </Badge>
                            </div>
                            <p class="mt-1 text-xs text-muted">
                                Builder application #{{ application.id }}
                                <span aria-hidden="true">·</span>
                                {{ application.minecraft_alias }}
                            </p>
                        </div>
                    </div>

                    <div class="flex shrink-0 flex-wrap items-center gap-x-3 gap-y-1 text-xs text-muted sm:justify-end">
                        <span>{{ format(application.created_at) }}</span>
                        <span>
                            {{ application.closed_at
                                ? `Reviewed in ${distance(application.created_at, application.closed_at)}`
                                : `Waiting ${distanceFromNow(application.created_at)}` }}
                        </span>
                    </div>
                </div>

                <div class="mt-4 rounded-none border border-border bg-surface-muted p-3">
                    <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-subtle">
                        Build summary
                    </p>
                    <p class="line-clamp-2 whitespace-pre-wrap text-sm leading-6 text-foreground">
                        {{ application.build_description }}
                    </p>
                </div>

                <div class="mt-3 flex flex-wrap items-center justify-between gap-2">
                    <p class="truncate text-xs text-muted">
                        Build location: {{ application.build_location }}
                    </p>
                    <span class="text-sm font-semibold text-primary-active group-hover:underline">
                        {{ application.status === BuilderRankApplicationStatus.pending ? 'Review application' : 'View application' }}
                    </span>
                </div>
            </Link>
        </li>
    </ul>

    <div v-else class="rounded-xl border border-dashed border-border-strong bg-surface p-8 text-center">
        <h2 class="text-base font-semibold text-foreground">No applications to review</h2>
        <p class="mt-1 text-sm text-muted">New builder applications will appear here.</p>
    </div>
</template>
