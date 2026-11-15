<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import Card from '../../Components/Card.vue'
import FilledButton from '../../Components/FilledButton.vue'
import usePermissions from '../../Composables/usePermissions'
import BroadcastStatus from './Components/BroadcastStatus.vue'

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
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">Player Mail</h1>
                <p class="mt-1 text-sm text-gray-500">Announcements sent to Minecraft players</p>
            </div>
            <Link v-if="can('web.manage.player_mail.send')" href="/manage/player-mail/create">
                <FilledButton variant="primary">New announcement</FilledButton>
            </Link>
        </div>

        <Card>
            <form class="flex flex-col gap-3 border-b border-gray-200 p-4 dark:border-gray-700 sm:flex-row" @submit.prevent="filterBroadcasts">
                <label class="min-w-0 flex-1">
                    <span class="sr-only">Search by subject</span>
                    <input
                        v-model="search"
                        type="search"
                        maxlength="191"
                        placeholder="Search subjects"
                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >
                </label>
                <label>
                    <span class="sr-only">Filter by status</span>
                    <select
                        v-model="status"
                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >
                        <option value="">All statuses</option>
                        <option value="queued">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="completed">Completed</option>
                        <option value="failed">Failed</option>
                    </select>
                </label>
                <FilledButton variant="secondary" type="submit">
                    Apply filters
                </FilledButton>
            </form>

            <div v-if="broadcasts.data.length === 0" class="px-6 py-14 text-center">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                    {{ broadcasts.total === 0 && !hasFilters ? 'No announcements yet' : 'No announcements match these filters' }}
                </h2>
                <p class="mt-2 text-sm text-gray-500">
                    {{ broadcasts.total === 0 && !hasFilters ? 'Your sent and processing announcements will appear here.' : 'Try a different subject or status.' }}
                </p>
                <Link v-if="broadcasts.total === 0 && !hasFilters && can('web.manage.player_mail.send')" href="/manage/player-mail/create" class="mt-5 inline-block">
                    <FilledButton variant="primary">Compose announcement</FilledButton>
                </Link>
            </div>

            <template v-else>
                <div class="divide-y divide-gray-200 dark:divide-gray-700 md:hidden">
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
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-100 text-xs uppercase text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="whitespace-nowrap px-4 py-3">Subject</th>
                                <th class="whitespace-nowrap px-4 py-3">Sender</th>
                                <th class="whitespace-nowrap px-4 py-3">Recipients</th>
                                <th class="whitespace-nowrap px-4 py-3">Status</th>
                                <th class="whitespace-nowrap px-4 py-3">Created</th>
                                <th class="px-4 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="broadcast in broadcasts.data" :key="broadcast.id" class="border-b border-gray-100 last:border-0 dark:border-gray-700">
                                <td class="max-w-xs truncate px-4 py-3 font-medium text-gray-900 dark:text-white">{{ broadcast.subject }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-gray-500">{{ broadcast.sender }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-gray-500">{{ broadcast.recipients_count }}</td>
                                <td class="px-4 py-3"><BroadcastStatus :status="broadcast.status" /></td>
                                <td class="whitespace-nowrap px-4 py-3 text-gray-500">{{ formatDate(broadcast.created_at) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <Link
                                        :href="broadcastUrl(broadcast.id)"
                                        class="font-medium text-blue-700 hover:underline dark:text-blue-400"
                                    >
                                        View details
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 p-4 text-sm text-gray-500">
                    <span>Showing {{ broadcasts.from }}–{{ broadcasts.to }} of {{ broadcasts.total }}</span>
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
