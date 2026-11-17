<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import Badge from '../../../manage/Components/Badge.vue'
import Card from '../../../manage/Components/Card.vue'
import DataTable from '../../../manage/Components/DataTable.vue'
import ShowingCount from '../../../manage/Components/ShowingCount.vue'
import PageHeader from '../../../shared/Components/PageHeader.vue'
import { distance, distanceFromNow, format, timeAgo } from '../../../manage/Utilities/DateFormatter.js'
import { Player } from '../../../manage/Data/Player.js'

interface OpElevation {
    id: number,
    player: Player,
    reason: string,
    startedAt: string,
    endedAt: string,
    isActive: boolean,
}

interface PaginationLink {
    url: string | null,
    label: string,
    active: boolean,
}

interface PaginatedElevations {
    data: OpElevation[],
    from: number | null,
    to: number | null,
    total: number,
    last_page: number,
    links: PaginationLink[],
}

defineProps<{
    elevations: PaginatedElevations,
}>()

const fields = [
    { key: 'status', label: 'Status' },
    { key: 'player', label: 'Player' },
    { key: 'reason', label: 'Reason' },
    { key: 'startedAt', label: 'Started' },
    { key: 'endedAt', label: 'Ended / expires' },
    { key: 'duration', label: 'Duration' },
]

function duration(elevation: OpElevation): string {
    return elevation.isActive
        ? distanceFromNow(elevation.startedAt) ?? 'Unavailable'
        : distance(elevation.startedAt, elevation.endedAt) ?? 'Unavailable'
}

function paginationLabel(label: string): string {
    return label.replace(/&laquo;|&raquo;/g, '').trim()
}
</script>

<template>
    <Head title="OP Elevations" />

    <div class="space-y-6">
        <PageHeader
            title="OP Elevations"
            description="Review who temporarily received server operator privileges."
        />

        <Card padding="none" class="overflow-hidden rounded-[var(--radius-md)] border border-border bg-surface">
            <DataTable :fields="fields" :rows="elevations.data" empty-message="No operator elevations have been recorded.">
                <template #status="{ item }">
                    <Badge v-if="item.isActive" variant="danger" size="sm">Active</Badge>
                    <Badge v-else variant="secondary" size="sm">Ended</Badge>
                </template>
                <template #player="{ item }">
                    <Link :href="`/manage/players/${item.player.id}`" class="font-medium text-foreground">
                        {{ item.player.alias ?? "No alias" }}
                    </Link>
                </template>

                <template #reason="{ item }">
                    <span class="max-w-md whitespace-normal text-muted">{{ item.reason }}</span>
                </template>

                <template #startedAt="{ item }">
                    <time :datetime="item.startedAt" :title="timeAgo(item.startedAt) ?? undefined">
                        {{ format(item.startedAt) }}
                    </time>
                </template>

                <template #endedAt="{ item }">
                    <time :datetime="item.endedAt" :title="timeAgo(item.endedAt) ?? undefined">
                        {{ item.isActive ? 'Expires' : 'Ended' }} {{ format(item.endedAt) }}
                    </time>
                </template>

                <template #duration="{ item }">
                    {{ duration(item) }}
                </template>
            </DataTable>

            <div class="flex flex-wrap items-center justify-between gap-4 border-t border-border p-4">
                <ShowingCount :from="elevations.from" :to="elevations.to" :total="elevations.total" />

                <nav
                    v-if="elevations.last_page > 1"
                    aria-label="OP elevation pages"
                    class="flex flex-wrap items-center justify-end gap-1"
                >
                    <template v-for="(link, index) in elevations.links" :key="`${index}-${link.label}`">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            :aria-current="link.active ? 'page' : undefined"
                            class="inline-flex min-h-9 min-w-9 items-center justify-center rounded-md border px-3 text-sm transition-colors focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus"
                            :class="link.active
                                ? 'border-primary bg-primary text-primary-foreground'
                                : 'border-border bg-surface text-foreground hover:bg-surface-hover'"
                        >
                            {{ paginationLabel(link.label) }}
                        </Link>
                        <span
                            v-else
                            aria-disabled="true"
                            class="inline-flex min-h-9 min-w-9 items-center justify-center rounded-md border border-border bg-surface-muted px-3 text-sm text-subtle"
                        >
                            {{ paginationLabel(link.label) }}
                        </span>
                    </template>
                </nav>
            </div>
        </Card>
    </div>
</template>
