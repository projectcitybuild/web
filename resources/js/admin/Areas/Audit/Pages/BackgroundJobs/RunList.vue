<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, reactive, watch } from 'vue'
import { Routes } from '../../../../Navigation/Routes'
import Badge from '../../../../Components/Badge.vue'
import DataTable from '../../../../Components/DataTable.vue'
import ListPage from '../../../../Components/ListPage.vue'
import LinkButton from '../../../../Components/LinkButton.vue'
import OutlinedButton from '../../../../Components/OutlinedButton.vue'
import PageHeader from '../../../../Components/PageHeader.vue'
import { useServerListSorting } from '../../../../Composables/useTableSorting'
import { format, timeAgo } from '../../../../Utilities/DateFormatter'

const props = defineProps<AdminSharedPageProps & AdminInertiaPageProps['audit/BackgroundJobs/RunList']>()
const filters = reactive({ ...props.filters })
const onSort = useServerListSorting(() => ({ sort: props.sort, direction: props.direction }))

const activeFilterCount = computed(() => [
    filters.search,
    filters.status,
    filters.queue,
].filter(Boolean).length)

watch(() => props.filters, (currentFilters) => {
    filters.search = currentFilters.search
    filters.status = currentFilters.status
    filters.queue = currentFilters.queue
}, { deep: true })

const fields = [
    { key: 'job_name', label: 'Job' },
    { key: 'status', label: 'Status' },
    { key: 'queue', label: 'Queue' },
    { key: 'attempts', label: 'Attempts' },
    { key: 'duration_ms', label: 'Duration' },
    { key: 'queued_at', label: 'Queued' },
]

const statuses = [
    { value: 'queued', label: 'Queued' },
    { value: 'running', label: 'Running' },
    { value: 'succeeded', label: 'Successful' },
    { value: 'failed', label: 'Failed' },
] as const

function visitFilteredResults(): void {
    const query = Object.fromEntries(
        Object.entries(filters).filter(([, value]) => value !== '')
    )

    if (props.sort && props.direction) {
        Object.assign(query, { sort: props.sort, direction: props.direction })
    }

    router.get(props.indexUrl, query, {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    })
}

function clearFilters(): void {
    filters.search = ''
    filters.status = ''
    filters.queue = ''
    visitFilteredResults()
}

function paginationLabel(label: string): string {
    return label.replace(/&laquo;|&raquo;/g, '').trim()
}

function statusVariant(status: BackgroundJobRunSummary['status']): 'success' | 'danger' | 'warning' | 'info' {
    switch (status) {
        case 'succeeded':
            return 'success'
        case 'failed':
            return 'danger'
        case 'running':
            return 'warning'
        case 'queued':
            return 'info'
    }
}

function jobName(name: string): string {
    return name.split('\\').pop() ?? name
}

function durationLabel(milliseconds: number | null): string {
    if (milliseconds === null) {
        return '—'
    }

    return milliseconds < 1000
        ? `${milliseconds} ms`
        : `${(milliseconds / 1000).toFixed(1)} s`
}
</script>

<template>
    <Head title="Background Jobs" />

    <div class="space-y-6">
        <PageHeader title="Background Jobs" description="Browse tracked background job runs.">
            <template #actions>
                <Link
                    :href="Routes.audit.backgroundJobs.throughput"
                    class="inline-flex min-h-10 items-center rounded-md border border-border-strong px-3 text-sm font-medium text-foreground hover:bg-surface-hover focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus"
                >
                    Throughput
                </Link>
            </template>
        </PageHeader>

        <ListPage
            :from="runs.from"
            :to="runs.to"
            :total="runs.total"
            count-label="job runs"
            :filter-count="activeFilterCount"
        >
            <template #filters>
                <form class="space-y-4" @submit.prevent="visitFilteredResults">
                    <div class="grid gap-4 sm:grid-cols-3">
                        <label for="job-search" class="flex flex-col gap-1.5 text-sm font-medium text-foreground">
                            Search
                            <input
                                id="job-search"
                                v-model.trim="filters.search"
                                type="search"
                                maxlength="255"
                                placeholder="Job name or queue"
                                class="min-h-10 w-full rounded-md border border-border-strong bg-surface px-3 text-sm text-foreground placeholder:text-muted focus:border-primary focus:ring-focus"
                            >
                        </label>

                        <label for="job-status" class="flex flex-col gap-1.5 text-sm font-medium text-foreground">
                            Status
                            <select
                                id="job-status"
                                v-model="filters.status"
                                class="min-h-10 w-full rounded-md border border-border-strong bg-surface px-3 text-sm text-foreground focus:border-primary focus:ring-focus"
                            >
                                <option value="">All statuses</option>
                                <option v-for="status in statuses" :key="status.value" :value="status.value">
                                    {{ status.label }}
                                </option>
                            </select>
                        </label>

                        <label for="job-queue" class="flex flex-col gap-1.5 text-sm font-medium text-foreground">
                            Queue
                            <select
                                id="job-queue"
                                v-model="filters.queue"
                                class="min-h-10 w-full rounded-md border border-border-strong bg-surface px-3 text-sm text-foreground focus:border-primary focus:ring-focus"
                            >
                                <option value="">All queues</option>
                                <option v-for="queue in props.queueOptions" :key="queue.value" :value="queue.value">
                                    {{ queue.label }}
                                </option>
                            </select>
                        </label>
                    </div>

                    <div class="flex flex-wrap justify-end gap-2">
                        <OutlinedButton variant="secondary" :disabled="activeFilterCount === 0" @click="clearFilters">
                            Clear filters
                        </OutlinedButton>
                        <OutlinedButton variant="secondary" type="submit">
                            Apply filters
                        </OutlinedButton>
                    </div>
                </form>
            </template>

            <DataTable
                :fields="fields"
                :rows="runs.data"
                :sort="props.sort"
                :direction="props.direction"
                :on-sort="onSort"
                empty-message="No job runs match these filters."
            >
                <template #job_name="{ item }">
                    <span class="font-medium text-foreground">{{ jobName(item.job_name) }}</span>
                </template>
                <template #status="{ item }">
                    <Badge :variant="statusVariant(item.status)" size="sm">
                        {{ item.status === 'succeeded' ? 'Successful' : item.status.charAt(0).toUpperCase() + item.status.slice(1) }}
                    </Badge>
                </template>
                <template #queue="{ item }">
                    {{ item.queue_label }}
                </template>
                <template #duration_ms="{ item }">
                    {{ durationLabel(item.duration_ms) }}
                </template>
                <template #queued_at="{ item }">
                    <time v-if="item.queued_at" :datetime="item.queued_at" :title="format(item.queued_at) ?? undefined">
                        {{ timeAgo(item.queued_at) }}
                    </time>
                    <span v-else class="text-muted">Not available</span>
                </template>
                <template #actions="{ item }">
                    <LinkButton :href="item.show_url" variant="outline" size="sm">
                        View
                    </LinkButton>
                </template>
            </DataTable>

            <template #footer>
                <div class="flex flex-wrap items-center justify-end gap-4">
                    <nav
                        v-if="runs.last_page > 1"
                        aria-label="Background job pages"
                        class="flex flex-wrap items-center justify-end gap-1"
                    >
                        <template v-for="(link, index) in runs.links" :key="`${index}-${link.label}`">
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
            </template>
        </ListPage>
    </div>
</template>
