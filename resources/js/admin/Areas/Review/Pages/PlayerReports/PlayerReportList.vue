<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { Routes } from '../../../../Navigation/Routes'
import Badge from '../../../../Components/Badge.vue'
import MinecraftAvatar from '../../../../Components/MinecraftAvatar.vue'
import PageHeader from '../../../../Components/PageHeader.vue'
import SuccessAlert from '../../../../Components/SuccessAlert.vue'
import { format } from '../../../../Utilities/DateFormatter'

const props = defineProps<AdminPageProps<'review/PlayerReports/PlayerReportList'>>()

const statusLinks = [
    { label: 'All reports', href: Routes.review.playerReports.list },
    { label: 'Open', href: `${Routes.review.playerReports.list}?status=open` },
    { label: 'Completed', href: `${Routes.review.playerReports.list}?status=completed` },
]
</script>

<template>
    <Head title="Player Reports" />

    <div class="space-y-4">
        <PageHeader
            title="Player Reports"
            description="Review reports submitted by players and follow up with reporters."
        />

        <SuccessAlert v-if="success" :message="success" />

        <nav aria-label="Filter reports" class="flex flex-wrap gap-2">
            <Link
                v-for="item in statusLinks"
                :key="item.label"
                :href="item.href"
                class="rounded-md border px-3 py-2 text-sm font-medium"
                :class="(item.label === 'All reports' && !filters.status)
                    || (item.label === 'Open' && filters.status === 'open')
                    || (item.label === 'Completed' && filters.status === 'completed')
                    ? 'border-primary bg-primary-soft text-primary-active'
                    : 'border-border bg-surface text-foreground hover:bg-surface-hover'"
            >
                {{ item.label }}
            </Link>
        </nav>

        <ul v-if="reports.data.length" class="space-y-3">
            <li v-for="report in reports.data" :key="report.id">
                <Link
                    :href="Routes.review.playerReports.show(report.id)"
                    class="block rounded-xl border border-border bg-surface p-4 transition-colors hover:border-border-strong hover:bg-surface-hover focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus"
                >
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="truncate text-base font-semibold text-foreground">
                                #{{ report.id }} · {{ report.subject }}
                            </h2>
                            <p class="mt-1 text-sm text-muted">
                                {{ report.reporter_name }} reported {{ report.reported_player_name }}
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <Badge :variant="report.status === 'open' ? 'warning' : 'success'" size="sm">
                                {{ report.status === 'open' ? 'Open' : 'Completed' }}
                            </Badge>
                            <Badge v-if="report.visibility === 'administrators'" variant="danger" size="sm">
                                Administrators only
                            </Badge>
                        </div>
                    </div>

                    <p class="mt-3 line-clamp-2 whitespace-pre-wrap text-sm leading-6 text-foreground">
                        {{ report.details }}
                    </p>

                    <div class="mt-3 flex flex-wrap items-center justify-between gap-3 text-xs text-muted">
                        <div v-if="report.assignees.length" class="flex flex-wrap items-center gap-2" aria-label="Assigned users">
                            <span class="sr-only">Assigned to:</span>
                            <div
                                v-for="assignee in report.assignees"
                                :key="assignee.id"
                                class="flex items-center gap-1.5"
                                :title="assignee.name"
                            >
                                <MinecraftAvatar
                                    v-if="assignee.uuid"
                                    :uuid="assignee.uuid"
                                    :size="24"
                                    class="shrink-0 overflow-hidden rounded-full"
                                />
                                <span
                                    v-else
                                    class="flex size-6 shrink-0 items-center justify-center rounded-full bg-primary/10 text-xs font-semibold text-primary"
                                    aria-hidden="true"
                                >
                                    {{ (assignee.alias ?? assignee.name).charAt(0).toUpperCase() }}
                                </span>
                                <span>{{ assignee.alias ?? assignee.name }}</span>
                            </div>
                        </div>
                        <span v-else>Unassigned</span>
                        <span>Submitted {{ format(report.created_at) }}</span>
                    </div>
                </Link>
            </li>
        </ul>

        <div v-else class="rounded-xl border border-dashed border-border-strong bg-surface p-8 text-center">
            <h2 class="text-base font-semibold text-foreground">No player reports found</h2>
            <p class="mt-1 text-sm text-muted">New reports will appear here.</p>
        </div>

        <nav v-if="reports.links.length > 3" aria-label="Report pages" class="flex flex-wrap gap-2">
            <Link
                v-for="link in reports.links"
                :key="link.label"
                :href="link.url ?? '#'"
                :preserve-scroll="true"
                class="rounded border px-3 py-2 text-sm"
                :class="link.active
                    ? 'border-primary bg-primary text-primary-foreground'
                    : 'border-border bg-surface text-foreground'"
                :aria-disabled="link.url === null"
                v-html="link.label"
            />
        </nav>
    </div>
</template>
