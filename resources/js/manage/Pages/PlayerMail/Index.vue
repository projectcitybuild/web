<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import Card from '../../Components/Card.vue'
import FilledButton from '../../Components/FilledButton.vue'
import usePermissions from '../../Composables/usePermissions'
import BroadcastStatus from './Components/BroadcastStatus.vue'
import FilterPanel from '../../Components/FilterPanel.vue'
import ShowingCount from '../../Components/ShowingCount.vue'
import Table from '../../../shared/Components/Table.vue'
import TableCell from '../../../shared/Components/TableCell.vue'
import TableHead from '../../../shared/Components/TableHead.vue'
import TableRow from '../../../shared/Components/TableRow.vue'

interface Broadcast {
    id: number,
    subject: string,
    sender: string,
    recipients_count: number,
    status: string,
    created_at: string,
}

interface Props {
    broadcasts: {
        data: Broadcast[],
        current_page: number,
        last_page: number,
        from: number | null,
        to: number | null,
        total: number,
    },
    filters: {
        search: string,
        status: string,
    },
}

const props = defineProps<Props>()
const search = ref(props.filters.search)
const status = ref(props.filters.status)
const { can } = usePermissions()
const hasFilters = computed(() => Boolean(props.filters.search || props.filters.status))
const activeFilterCount = computed(() => [Boolean(search.value), Boolean(status.value)].filter(Boolean).length)

function filterBroadcasts() {
    router.get('/manage/player-mail', {
        search: search.value || undefined,
        status: status.value || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

function clearFilters() {
    search.value = ''
    status.value = ''
    filterBroadcasts()
}

function pageUrl(page: number): string {
    const query = new URLSearchParams()

    if (props.filters.search) query.set('search', props.filters.search)
    if (props.filters.status) query.set('status', props.filters.status)
    query.set('page', String(page))

    return `/manage/player-mail?${query.toString()}`
}

function broadcastUrl(id: number): string {
    const query = new URLSearchParams()

    if (props.filters.search) query.set('search', props.filters.search)
    if (props.filters.status) query.set('status', props.filters.status)
    query.set('page', String(props.broadcasts.current_page))

    return `/manage/player-mail/${id}?${query.toString()}`
}

function formatDate(value: string): string {
    return new Intl.DateTimeFormat(undefined, {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value))
}
</script>

<template>
    <Head title="Player Mail" />

    <section class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-foreground">Player Mail</h1>
                <p class="mt-1 text-sm text-muted">Announcements sent to Minecraft players</p>
            </div>
            <Link v-if="can('web.manage.player_mail.send')" href="/manage/player-mail/create">
                <FilledButton variant="primary">New announcement</FilledButton>
            </Link>
        </div>

        <Card>
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-border p-4">
                <p class="text-sm text-muted">
                    {{ broadcasts.total }} {{ broadcasts.total === 1 ? 'announcement' : 'announcements' }}
                    <span v-if="hasFilters">matching filters</span>
                </p>
                <FilterPanel
                    id="broadcast-filters"
                    :active-count="activeFilterCount"
                    @apply="filterBroadcasts"
                    @clear="clearFilters"
                >
                    <form class="grid gap-4" @submit.prevent="filterBroadcasts">
                        <label class="flex flex-col gap-1.5 text-sm font-medium text-gray-700 dark:text-gray-200">
                            Search subject
                            <input
                                v-model="search"
                                type="search"
                                maxlength="191"
                                placeholder="Search announcement subjects"
                                class="min-h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            >
                        </label>
                        <label class="flex flex-col gap-1.5 text-sm font-medium text-gray-700 dark:text-gray-200">
                            Delivery status
                            <select
                                v-model="status"
                                class="min-h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            >
                                <option value="">All statuses</option>
                                <option value="queued">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="completed">Completed</option>
                                <option value="failed">Failed</option>
                            </select>
                        </label>
                    </form>
                </FilterPanel>
            </div>

            <div v-if="broadcasts.data.length === 0" class="px-6 py-14 text-center">
                <h2 class="text-base font-semibold text-foreground">
                    {{ broadcasts.total === 0 && !hasFilters ? 'No announcements yet' : 'No announcements match these filters' }}
                </h2>
                <p class="mt-2 text-sm text-muted">
                    {{ broadcasts.total === 0 && !hasFilters ? 'Your sent and processing announcements will appear here.' : 'Try a different subject or status.' }}
                </p>
                <Link v-if="broadcasts.total === 0 && !hasFilters && can('web.manage.player_mail.send')" href="/manage/player-mail/create" class="mt-5 inline-block">
                    <FilledButton variant="primary">Compose announcement</FilledButton>
                </Link>
            </div>

            <template v-else>
                <div class="divide-y divide-border md:hidden">
                    <Link
                        v-for="broadcast in broadcasts.data"
                        :key="broadcast.id"
                        :href="broadcastUrl(broadcast.id)"
                        class="block space-y-2 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/40"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <span class="font-semibold text-gray-900 dark:text-white">{{ broadcast.subject }}</span>
                            <BroadcastStatus :status="broadcast.status" />
                        </div>
                        <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500">
                            <span>{{ broadcast.sender }}</span>
                            <span>{{ broadcast.recipients_count }} delivered</span>
                            <time :datetime="broadcast.created_at">{{ formatDate(broadcast.created_at) }}</time>
                        </div>
                    </Link>
                </div>

                <div class="hidden overflow-x-auto md:block">
                    <Table>
                        <template #header>
                            <TableRow :hoverable="false">
                                <TableHead>Status</TableHead>
                                <TableHead>Subject</TableHead>
                                <TableHead>Sender</TableHead>
                                <TableHead>Recipients</TableHead>
                                <TableHead>Created</TableHead>
                                <TableHead align="end">Action</TableHead>
                            </TableRow>
                        </template>
                        <TableRow v-for="broadcast in broadcasts.data" :key="broadcast.id">
                            <TableCell><BroadcastStatus :status="broadcast.status" /></TableCell>
                            <TableCell class="max-w-xs truncate font-medium">{{ broadcast.subject }}</TableCell>
                            <TableCell class="whitespace-nowrap text-muted">{{ broadcast.sender }}</TableCell>
                            <TableCell class="whitespace-nowrap text-muted">{{ broadcast.recipients_count }}</TableCell>
                            <TableCell class="whitespace-nowrap text-muted">{{ formatDate(broadcast.created_at) }}</TableCell>
                            <TableCell align="end">
                                    <Link
                                        :href="broadcastUrl(broadcast.id)"
                                        class="font-medium text-blue-700 hover:underline dark:text-blue-400"
                                    >
                                        View details
                                    </Link>
                            </TableCell>
                        </TableRow>
                    </Table>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 p-4 text-sm text-gray-500">
                    <ShowingCount :from="broadcasts.from" :to="broadcasts.to" :total="broadcasts.total" />
                    <div class="flex items-center gap-2">
                        <Link v-if="broadcasts.current_page > 1" :href="pageUrl(broadcasts.current_page - 1)" class="px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Previous</Link>
                        <span>Page {{ broadcasts.current_page }} of {{ broadcasts.last_page }}</span>
                        <Link v-if="broadcasts.current_page < broadcasts.last_page" :href="pageUrl(broadcasts.current_page + 1)" class="px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Next</Link>
                    </div>
                </div>
            </template>
        </Card>
    </section>
</template>
