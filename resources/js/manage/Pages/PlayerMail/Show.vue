<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { onBeforeUnmount, onMounted } from 'vue'
import BackButton from '../../Components/BackButton.vue'
import Card from '../../Components/Card.vue'
import BroadcastProgress from './Components/BroadcastProgress.vue'
import BroadcastStatus from './Components/BroadcastStatus.vue'
import MailPreview from './Components/MailPreview.vue'

interface Broadcast {
    id: number,
    subject: string,
    body: string,
    sender: string,
    status: string,
    recipients_count: number,
    created_at: string,
    started_at: string | null,
    completed_at: string | null,
}

interface Props {
    broadcast: Broadcast,
    audienceEstimate: number,
    backUrl: string,
}

const props = defineProps<Props>()
let refreshTimer: ReturnType<typeof setInterval> | undefined

function formatDate(value: string | null): string {
    if (!value) return 'Not started'

    return new Intl.DateTimeFormat(undefined, {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value))
}

onMounted(() => {
    refreshTimer = setInterval(() => {
        if (props.broadcast.status === 'queued' || props.broadcast.status === 'processing') {
            router.reload({
                only: ['broadcast', 'audienceEstimate'],
                preserveScroll: true,
            })
        }
    }, 5000)
})

onBeforeUnmount(() => {
    if (refreshTimer) clearInterval(refreshTimer)
})
</script>

<template>
    <Head :title="`Player Mail: ${broadcast.subject}`" />

    <section>
        <Card>
            <div class="space-y-6 p-5 sm:p-8">
                <BackButton :href="backUrl" />

                <header class="flex flex-wrap items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-sm text-gray-500">Broadcast #{{ broadcast.id }}</p>
                        <h1 class="mt-1 break-words text-xl font-bold text-gray-900 dark:text-white">{{ broadcast.subject }}</h1>
                    </div>
                    <BroadcastStatus :status="broadcast.status" />
                </header>

                <div class="grid gap-5 lg:grid-cols-[minmax(0,1.4fr)_minmax(16rem,0.6fr)]">
                    <div class="space-y-3">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Announcement</h2>
                        <MailPreview :subject="broadcast.subject" :body="broadcast.body" />
                    </div>

                    <div class="space-y-5 border-t border-border pt-5 lg:border-l lg:border-t-0 lg:pl-6 lg:pt-0">
                        <div>
                            <h2 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">Delivery</h2>
                            <BroadcastProgress
                                :delivered="broadcast.recipients_count"
                                :audience-size="audienceEstimate"
                                :status="broadcast.status"
                            />
                        </div>
                        <dl class="grid grid-cols-2 gap-4 text-sm">
                            <div class="col-span-2">
                                <dt class="text-gray-500">Sent by</dt>
                                <dd class="mt-1 font-medium text-gray-900 dark:text-white">{{ broadcast.sender }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Created</dt>
                                <dd class="mt-1 font-medium text-gray-900 dark:text-white">{{ formatDate(broadcast.created_at) }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Started</dt>
                                <dd class="mt-1 font-medium text-gray-900 dark:text-white">{{ formatDate(broadcast.started_at) }}</dd>
                            </div>
                            <div class="col-span-2">
                                <dt class="text-gray-500">Completed</dt>
                                <dd class="mt-1 font-medium text-gray-900 dark:text-white">{{ formatDate(broadcast.completed_at) }}</dd>
                            </div>
                        </dl>

                        <p v-if="broadcast.status === 'failed'" class="border-l-4 border-red-500 bg-red-50 px-3 py-2 text-sm text-red-800 dark:bg-red-950 dark:text-red-200">
                            This broadcast failed. Retrying is not currently supported.
                        </p>
                        <p v-else-if="broadcast.status === 'queued' || broadcast.status === 'processing'" class="text-xs text-gray-500" role="status">
                            Delivery details refresh automatically.
                        </p>
                    </div>
                </div>
            </div>
        </Card>
    </section>
</template>
