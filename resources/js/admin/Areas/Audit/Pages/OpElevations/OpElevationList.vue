<script setup lang="ts">
import { Routes } from '../../../../Navigation/Routes'
import { Head, Link } from '@inertiajs/vue3'
import Badge from '../../../../Components/Badge.vue'
import DataTable from '../../../../Components/DataTable.vue'
import ListPage from '../../../../Components/ListPage.vue'
import PageHeader from '../../../../Components/PageHeader.vue'
import { distance, distanceFromNow, format, timeAgo } from '../../../../Utilities/DateFormatter.js'
import { useServerListSorting } from '../../../../Composables/useTableSorting'

const props = defineProps<AdminSharedPageProps & AdminInertiaPageProps['audit/OpElevations/OpElevationList']>()
const onSort = useServerListSorting(() => ({ sort: props.sort, direction: props.direction }))

const fields = [
    { key: 'status', label: 'Status', sortable: false },
    { key: 'player', label: 'Player', sortable: false },
    { key: 'reason', label: 'Reason' },
    { key: 'startedAt', label: 'Started' },
    { key: 'endedAt', label: 'Ended / Expires' },
    { key: 'duration', label: 'Duration', sortable: false },
]

function duration(elevation: App.Http.Data.AdminPlayerOpElevationData): string {
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

        <ListPage
            :from="props.elevations.from"
            :to="props.elevations.to"
            :total="props.elevations.total"
            count-label="operator elevations"
        >
            <DataTable
                :fields="fields"
                :rows="props.elevations.data"
                :sort="props.sort"
                :direction="props.direction"
                :on-sort="onSort"
                empty-message="No operator elevations have been recorded."
            >
                <template #status="{ item }">
                    <Badge v-if="item.isActive" variant="danger" size="sm">Active</Badge>
                    <Badge v-else variant="default" size="sm">Ended</Badge>
                </template>
                <template #player="{ item }">
                    <Link :href="Routes.manage.players.show(item.player.id)" class="font-medium text-foreground">
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

            <div class="flex flex-wrap items-center justify-end gap-4 border-t border-border p-4">
                <nav
                    v-if="props.elevations.last_page > 1"
                    aria-label="OP elevation pages"
                    class="flex flex-wrap items-center justify-end gap-1"
                >
                    <template v-for="(link, index) in props.elevations.links" :key="`${index}-${link.label}`">
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
        </ListPage>
    </div>
</template>
