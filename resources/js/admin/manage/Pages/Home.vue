<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import Card from '../../shared/Components/Card.vue'
import Badge from '../../shared/Components/Badge.vue'
import PageHeader from '../../shared/Components/PageHeader.vue'
import MinecraftAvatar from '../Components/MinecraftAvatar.vue'
import { format, timeAgo } from '../Utilities/DateFormatter'

interface ActivityRecord {
    id: string,
    actor: string,
    avatarUuid: string | null,
    action: string,
    event: string,
    subject: string,
    createdAt: string,
    when: string,
}

interface Props {
    recentActivities?: ActivityRecord[] | null,
    activityUrl?: string | null,
}

const props = withDefaults(defineProps<Props>(), {
    recentActivities: null,
    activityUrl: null,
})

function eventVariant(event: string): 'default' | 'success' | 'info' | 'warning' | 'danger' {
    switch (event) {
        case 'created':
            return 'success'
        case 'updated':
            return 'info'
        case 'deleted':
            return 'danger'
        case 'restored':
            return 'warning'
        default:
            return 'default'
    }
}
</script>

<template>
    <Head title="Management" />

    <div class="space-y-6">
        <PageHeader title="Management" description="Manage the Project City Build platform." />

        <section v-if="props.recentActivities" aria-label="Recent activity" class="space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-base font-semibold text-foreground">Recent activity</h2>
                <Link
                    v-if="props.activityUrl"
                    :href="props.activityUrl"
                    class="text-sm font-medium text-foreground underline decoration-border underline-offset-4 hover:text-muted focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus"
                >
                    View audit log
                </Link>
            </div>

            <Card padding="none" class="overflow-hidden rounded-xl border border-border bg-surface">
                <ol v-if="props.recentActivities.length > 0">
                    <li
                        v-for="activity in props.recentActivities"
                        :key="activity.id"
                        class="flex items-start gap-3 border-b border-border p-4 last:border-b-0 sm:gap-4 sm:p-5"
                    >
                        <MinecraftAvatar
                            v-if="activity.avatarUuid"
                            :uuid="activity.avatarUuid"
                            :size="40"
                            class="size-10 shrink-0 overflow-hidden rounded-full"
                        />
                        <span
                            v-else
                            aria-hidden="true"
                            class="flex size-10 shrink-0 items-center justify-center rounded-full bg-surface-muted text-sm font-semibold text-muted"
                        >
                            {{ activity.actor.charAt(0).toUpperCase() }}
                        </span>
                        <div class="flex min-w-0 flex-1 flex-col gap-2 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
                            <div class="min-w-0 space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="break-words text-sm font-semibold text-foreground">{{ activity.action }}</h3>
                                    <Badge :variant="eventVariant(activity.event)" size="sm">{{ activity.event }}</Badge>
                                </div>
                                <p class="break-words text-sm text-muted">
                                    by <span class="font-medium text-foreground">{{ activity.actor }}</span>
                                    <span aria-hidden="true"> · </span>
                                    {{ activity.subject }}
                                </p>
                            </div>
                            <time
                                :datetime="activity.createdAt"
                                :title="format(activity.createdAt) ?? undefined"
                                class="flex shrink-0 flex-col text-xs text-muted sm:items-end"
                            >
                                <span class="font-medium text-foreground">{{ timeAgo(activity.createdAt) ?? activity.when }}</span>
                                <span>{{ format(activity.createdAt) }}</span>
                            </time>
                        </div>
                    </li>
                </ol>
                <p v-else class="p-4 text-sm text-muted">No changes have been recorded yet.</p>
            </Card>
        </section>
    </div>
</template>
