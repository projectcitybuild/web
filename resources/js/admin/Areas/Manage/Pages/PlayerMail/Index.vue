<script setup lang="ts">
import { Routes } from '../../../../Navigation/Routes'
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import Card from '../../../../Components/ManageCard.vue'
import FilledButton from '../../../../Components/FilledButton.vue'
import usePermissions from '../../../../Composables/usePermissions'
import BroadcastStatus from '../../../../Components/PlayerMail/BroadcastStatus.vue'
import FilterPanel from '../../../../Components/FilterPanel.vue'
import ShowingCount from '../../../../Components/ShowingCount.vue'
import Table from '../../../../Components/Table.vue'
import TableCell from '../../../../Components/TableCell.vue'
import TableHead from '../../../../Components/TableHead.vue'
import TableRow from '../../../../Components/TableRow.vue'
import PageHeader from '../../../../Components/PageHeader.vue'
import LinkButton from '../../../../Components/LinkButton.vue'
import EmptyState from '../../../../Components/EmptyState.vue'

const props = defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/PlayerMail/Index']>()
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

    return `${Routes.manage.playerMail.list}?${query.toString()}`
}

function broadcastUrl(id: number): string {
    const query = new URLSearchParams()

    if (props.filters.search) query.set('search', props.filters.search)
    if (props.filters.status) query.set('status', props.filters.status)
    query.set('page', String(props.broadcasts.current_page))

    return `${Routes.manage.playerMail.show(id)}?${query.toString()}`
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

    <div class="space-y-6">
        <PageHeader title="Player Mail" description="Review and manage announcements sent to Minecraft players.">
            <template #actions>
            <LinkButton v-if="can('web.manage.player_mail.send')" :href="Routes.manage.playerMail.create" variant="primary">New announcement</LinkButton>
            </template>
        </PageHeader>

        <Card padding="none">
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
                        <label class="flex flex-col gap-1.5 text-sm font-medium text-muted dark:text-muted">
                            Search subject
                            <input
                                v-model="search"
                                type="search"
                                maxlength="191"
                                placeholder="Search announcement subjects"
                                class="min-h-11 w-full rounded-lg border border-border-strong bg-white px-3 text-sm text-foreground focus:border-primary focus:ring-focus"
                            >
                        </label>
                        <label class="flex flex-col gap-1.5 text-sm font-medium text-muted dark:text-muted">
                            Delivery status
                            <select
                                v-model="status"
                                class="min-h-11 w-full rounded-lg border border-border-strong bg-white px-3 text-sm text-foreground focus:border-primary focus:ring-focus"
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

            <EmptyState
                v-if="broadcasts.data.length === 0"
                :title="broadcasts.total === 0 && !hasFilters ? 'No announcements yet' : 'No announcements match these filters'"
                :description="broadcasts.total === 0 && !hasFilters ? 'Your sent and processing announcements will appear here.' : 'Try a different subject or status.'"
            >
                <template v-if="broadcasts.total === 0 && !hasFilters && can('web.manage.player_mail.send')" #actions>
                    <LinkButton :href="Routes.manage.playerMail.create" variant="primary">
                        Compose announcement
                    </LinkButton>
                </template>
            </EmptyState>

            <template v-else>
                <div class="divide-y divide-border md:hidden">
                    <Link
                        v-for="broadcast in broadcasts.data"
                        :key="broadcast.id"
                        :href="broadcastUrl(broadcast.id)"
                        class="block space-y-2 p-4 hover:bg-surface-muted hover:bg-surface-hover"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <span class="font-semibold text-foreground">{{ broadcast.subject }}</span>
                            <BroadcastStatus :status="broadcast.status" />
                        </div>
                        <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-muted">
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
                                <LinkButton :href="broadcastUrl(broadcast.id)" variant="outline" size="sm">
                                    View details
                                </LinkButton>
                            </TableCell>
                        </TableRow>
                    </Table>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 p-4 text-sm text-muted">
                    <ShowingCount :from="broadcasts.from" :to="broadcasts.to" :total="broadcasts.total" />
                    <div class="flex items-center gap-2">
                        <LinkButton v-if="broadcasts.current_page > 1" :href="pageUrl(broadcasts.current_page - 1)" variant="ghost" size="sm" class="text-sm">Previous</LinkButton>
                        <span>Page {{ broadcasts.current_page }} of {{ broadcasts.last_page }}</span>
                        <LinkButton v-if="broadcasts.current_page < broadcasts.last_page" :href="pageUrl(broadcasts.current_page + 1)" variant="ghost" size="sm" class="text-sm">Next</LinkButton>
                    </div>
                </div>
            </template>
        </Card>
    </div>
</template>
