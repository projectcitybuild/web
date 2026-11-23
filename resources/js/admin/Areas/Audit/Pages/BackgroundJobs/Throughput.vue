<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { Routes } from '../../../../Navigation/Routes'
import Badge from '../../../../Components/Badge.vue'
import Card from '../../../../Components/Card.vue'
import ConfirmDialog from '../../../../Components/ConfirmDialog.vue'
import LineGraph from '../../../../Components/LineGraph.vue'
import OutlinedButton from '../../../../Components/OutlinedButton.vue'
import PageHeader from '../../../../Components/PageHeader.vue'
import { format, timeAgo } from '../../../../Utilities/DateFormatter'

type ChartSeries = {
    key: string
    label: string
    color: 'success' | 'danger'
    values: Array<{ timestamp: string; value: number }>
    hideWhenEmpty?: boolean
}
type TaskActionFeedback = { type: 'success' | 'error'; message: string }

const props = defineProps<AdminSharedPageProps & AdminInertiaPageProps['audit/BackgroundJobs/Throughput']>()

const selectedRange = ref(props.range)
const selectedQueue = ref('all')
const countdownNow = ref(Date.now())
const isRefreshing = ref(false)
const refreshError = ref(false)
const processingActions = ref<Record<string, 'queue' | 'run'>>({})
const taskActionFeedback = ref<Record<string, TaskActionFeedback>>({})
const pendingRunTask = ref<{ id: string; name: string } | null>(null)
const runTaskDialog = ref<InstanceType<typeof ConfirmDialog>>()
let refreshTimer: number | undefined
let countdownTimer: number | undefined

const rangeOptions = [
    { value: '1h', label: 'Last hour' },
    { value: '24h', label: 'Last 24 hours' },
    { value: '7d', label: 'Last 7 days' },
    { value: '30d', label: 'Last 30 days' },
] as const

const chartBuckets = computed(() => {
    if (selectedQueue.value === 'all') {
        return props.dashboard.buckets
    }

    const queueBuckets = props.dashboard.queueBuckets.find((item) => item.queue === selectedQueue.value)?.buckets

    return props.dashboard.buckets.map((bucket, index) => ({
        timestamp: bucket.timestamp,
        succeeded: queueBuckets?.[index]?.succeeded ?? 0,
        failed: queueBuckets?.[index]?.failed ?? 0,
    }))
})
const chartQueueLabel = computed(() => selectedQueue.value === 'all'
    ? 'All queues'
    : props.dashboard.queueBreakdown.find((item) => item.queue === selectedQueue.value)?.label ?? selectedQueue.value)
const chartSeries = computed<ChartSeries[]>(() => [
    {
        key: 'succeeded',
        label: 'Successful',
        color: 'success',
        values: chartBuckets.value.map((bucket) => ({ timestamp: bucket.timestamp, value: bucket.succeeded })),
    },
    {
        key: 'failed',
        label: 'Failed',
        color: 'danger',
        values: chartBuckets.value.map((bucket) => ({ timestamp: bucket.timestamp, value: bucket.failed })),
        hideWhenEmpty: true,
    },
])
const maximumQueueCount = computed(() => Math.max(1, ...props.dashboard.queueBreakdown.map(item => item.count)))

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

function statusLabel(status: BackgroundJobRunSummary['status']): string {
    return status === 'succeeded' ? 'Successful' : status.charAt(0).toUpperCase() + status.slice(1)
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

function pendingAge(timestamp: string | null): string {
    if (!timestamp) {
        return 'None'
    }

    return timeAgo(timestamp) ?? format(timestamp) ?? 'Unknown'
}

function taskCountdown(task: AdminInertiaPageProps['audit/BackgroundJobs/Throughput']['scheduledTasks'][number]): string {
    const elapsedSeconds = Math.floor((countdownNow.value - new Date(props.dashboard.generatedAt).getTime()) / 1000)
    const seconds = Math.max(0, task.secondsUntilNextRun - elapsedSeconds)

    if (seconds === 0) {
        return 'Due now'
    }

    const days = Math.floor(seconds / 86_400)
    const hours = Math.floor((seconds % 86_400) / 3_600)
    const minutes = Math.floor((seconds % 3_600) / 60)
    const remainingSeconds = seconds % 60
    const duration = days > 0
        ? `${days}d ${hours}h`
        : hours > 0
            ? `${hours}h ${minutes}m`
            : minutes > 0
                ? `${minutes}m ${remainingSeconds}s`
                : `${remainingSeconds}s`

    return `in ${duration}`
}

function queueTask(taskId: string): void {
    submitTaskAction(taskId, 'queue')
}

function requestRunNow(task: { id: string; name: string }): void {
    pendingRunTask.value = task
    runTaskDialog.value?.open()
}

function confirmRunNow(): void {
    const task = pendingRunTask.value

    runTaskDialog.value?.close()
    pendingRunTask.value = null

    if (task) {
        submitTaskAction(task.id, 'run')
    }
}

function submitTaskAction(taskId: string, action: 'queue' | 'run'): void {
    if (processingActions.value[taskId]) {
        return
    }

    processingActions.value = { ...processingActions.value, [taskId]: action }
    const feedback = { ...taskActionFeedback.value }
    delete feedback[taskId]
    taskActionFeedback.value = feedback

    router.post(
        action === 'queue'
            ? Routes.audit.backgroundJobs.queueTask(taskId)
            : Routes.audit.backgroundJobs.runTask(taskId),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                taskActionFeedback.value = {
                    ...taskActionFeedback.value,
                    [taskId]: {
                        type: 'success',
                        message: action === 'queue' ? 'Queued for execution.' : 'Task ran successfully.',
                    },
                }
            },
            onError: () => {
                taskActionFeedback.value = {
                    ...taskActionFeedback.value,
                    [taskId]: {
                        type: 'error',
                        message: 'The task request was rejected. Please try again.',
                    },
                }
            },
            onHttpException: () => {
                taskActionFeedback.value = {
                    ...taskActionFeedback.value,
                    [taskId]: {
                        type: 'error',
                        message: 'The task request failed. Refresh the page and try again.',
                    },
                }
            },
            onNetworkError: () => {
                taskActionFeedback.value = {
                    ...taskActionFeedback.value,
                    [taskId]: {
                        type: 'error',
                        message: 'Could not reach the server. Check your connection and try again.',
                    },
                }
            },
            onFinish: () => {
                const actions = { ...processingActions.value }
                delete actions[taskId]
                processingActions.value = actions
            },
        },
    )
}

function changeRange(): void {
    router.get(Routes.audit.backgroundJobs.throughput, { range: selectedRange.value }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

function refreshDashboard(): void {
    if (document.visibilityState !== 'visible' || isRefreshing.value) {
        return
    }

    isRefreshing.value = true

    router.reload({
        only: ['dashboard', 'scheduledTasks'],
        onSuccess: () => {
            refreshError.value = false
        },
        onError: () => {
            refreshError.value = true
        },
        onHttpException: () => {
            refreshError.value = true
        },
        onNetworkError: () => {
            refreshError.value = true
        },
        onFinish: () => {
            isRefreshing.value = false
        },
    })
}

onMounted(() => {
    refreshTimer = window.setInterval(refreshDashboard, 15_000)
    countdownTimer = window.setInterval(() => {
        countdownNow.value = Date.now()
    }, 1000)
    document.addEventListener('visibilitychange', refreshDashboard)
})

onBeforeUnmount(() => {
    if (refreshTimer) {
        clearInterval(refreshTimer)
    }

    if (countdownTimer) {
        clearInterval(countdownTimer)
    }

    document.removeEventListener('visibilitychange', refreshDashboard)
})
</script>

<template>
    <div class="space-y-6">
        <Head title="Background Job Throughput" />

        <PageHeader title="Throughput" description="Background task performance and queue health.">
            <template #actions>
                <label class="flex items-center gap-2 text-sm font-medium text-foreground">
                    <span class="sr-only">Time range</span>
                    <select
                        v-model="selectedRange"
                        aria-label="Time range"
                        class="min-h-10 rounded-md border border-border-strong bg-surface px-3 text-sm text-foreground focus:border-primary focus:ring-focus"
                        @change="changeRange"
                    >
                        <option v-for="option in rangeOptions" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </option>
                    </select>
                </label>
            </template>
        </PageHeader>

        <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-muted" role="status" aria-live="polite">
            <span v-if="refreshError" class="text-danger">Refresh failed. Showing the last successful update.</span>
            <span v-else>{{ isRefreshing ? 'Refreshing metrics…' : 'Updates every 15 seconds while this page is visible.' }}</span>
            <span>Last updated {{ format(dashboard.generatedAt) }}</span>
        </div>

        <div v-if="refreshError" class="rounded-lg border border-danger/30 bg-danger-soft px-4 py-3 text-sm text-danger" role="alert">
            The latest refresh could not be loaded. Existing metrics are still shown.
        </div>

        <div v-if="props.success" class="rounded-lg border border-success/30 bg-success-soft px-4 py-3 text-sm text-success" role="status">
            {{ props.success }}
        </div>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Job summary">
            <Card class="rounded-xl border border-border bg-surface">
                <p class="text-sm text-muted">Completed jobs</p>
                <p class="mt-2 text-2xl font-semibold text-foreground">{{ dashboard.summary.succeeded.toLocaleString() }}</p>
                <p class="mt-1 text-xs text-muted">Successful runs in this range</p>
            </Card>
            <Card class="rounded-xl border border-border bg-surface">
                <p class="text-sm text-muted">Success rate</p>
                <p class="mt-2 text-2xl font-semibold text-foreground">
                    {{ dashboard.summary.successRate === null ? '—' : `${dashboard.summary.successRate}%` }}
                </p>
                <p class="mt-1 text-xs text-muted">Of completed and failed runs</p>
            </Card>
            <Card class="rounded-xl border border-border bg-surface">
                <p class="text-sm text-muted">Failed jobs</p>
                <p class="mt-2 text-2xl font-semibold text-foreground">{{ dashboard.summary.failed.toLocaleString() }}</p>
                <p class="mt-1 text-xs text-muted">Runs that ended unsuccessfully</p>
            </Card>
            <Card class="rounded-xl border border-border bg-surface">
                <p class="text-sm text-muted">Average processing duration</p>
                <p class="mt-2 text-2xl font-semibold text-foreground">{{ durationLabel(dashboard.summary.averageDurationMs) }}</p>
                <p class="mt-1 text-xs text-muted">Across terminal runs in this range</p>
            </Card>
        </section>

        <Card class="space-y-4 rounded-xl border border-border bg-surface">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 class="text-base font-semibold text-foreground">Job throughput</h2>
                    <p class="mt-1 text-sm text-muted">Jobs completed per time bucket.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <label class="flex items-center gap-2 text-sm font-medium text-foreground">
                        <span>Queue</span>
                        <select
                            v-model="selectedQueue"
                            aria-label="Filter graph by queue"
                            class="min-h-10 rounded-md border border-border-strong bg-surface px-3 text-sm text-foreground focus:border-primary focus:ring-focus"
                        >
                            <option value="all">All queues</option>
                            <option v-for="queue in dashboard.queueBreakdown" :key="queue.queue" :value="queue.queue">
                                {{ queue.label }}
                            </option>
                        </select>
                    </label>
                </div>
            </div>

            <div v-if="isRefreshing" class="h-1 overflow-hidden rounded bg-surface-muted" aria-hidden="true">
                <div class="h-full w-1/3 animate-pulse rounded bg-primary" />
            </div>

            <LineGraph
                :series="chartSeries"
                :label="`Line chart of successful and failed jobs over time for ${chartQueueLabel}`"
            />
        </Card>

        <div class="grid gap-4 xl:grid-cols-2">
            <Card class="space-y-4 rounded-xl border border-border bg-surface">
                <div>
                    <h2 class="text-base font-semibold text-foreground">Completed jobs by queue</h2>
                    <p class="mt-1 text-sm text-muted">Successful and failed runs in the selected range.</p>
                </div>
                <ul class="space-y-4">
                    <li v-for="item in dashboard.queueBreakdown" :key="item.queue" class="space-y-1.5">
                        <div class="flex items-center justify-between gap-4 text-sm">
                            <span class="text-foreground">{{ item.label }}</span>
                            <span class="font-medium tabular-nums text-foreground">{{ item.count.toLocaleString() }}</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-surface-muted">
                            <div
                                class="h-full rounded-full bg-primary transition-[width]"
                                :style="{ width: `${(item.count / maximumQueueCount) * 100}%` }"
                            />
                        </div>
                    </li>
                </ul>
            </Card>

            <Card class="space-y-4 rounded-xl border border-border bg-surface">
                <div>
                    <h2 class="text-base font-semibold text-foreground">Queue health</h2>
                    <p class="mt-1 text-sm text-muted">Lifecycle-tracked counts using the {{ dashboard.queueHealth.driver }} queue driver.</p>
                </div>
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-lg bg-surface-muted p-3">
                        <dt class="text-sm text-muted">Tracked pending</dt>
                        <dd class="mt-1 text-xl font-semibold text-foreground">{{ dashboard.queueHealth.pending.toLocaleString() }}</dd>
                    </div>
                    <div class="rounded-lg bg-surface-muted p-3">
                        <dt class="text-sm text-muted">Tracked running</dt>
                        <dd class="mt-1 text-xl font-semibold text-foreground">{{ dashboard.queueHealth.running.toLocaleString() }}</dd>
                    </div>
                    <div class="rounded-lg bg-surface-muted p-3">
                        <dt class="text-sm text-muted">Oldest pending age</dt>
                        <dd class="mt-1 text-sm font-semibold text-foreground">{{ pendingAge(dashboard.queueHealth.oldestPendingAt) }}</dd>
                    </div>
                    <div class="rounded-lg bg-surface-muted p-3">
                        <dt class="text-sm text-muted">Failed awaiting attention</dt>
                        <dd class="mt-1 text-xl font-semibold text-foreground">{{ dashboard.queueHealth.failed.toLocaleString() }}</dd>
                    </div>
                </dl>
                <p class="text-xs text-muted">Pending and running figures reflect recorded lifecycle state, not an independent backend queue inspection.</p>
            </Card>
        </div>

        <Card class="space-y-4 rounded-xl border border-border bg-surface">
            <div>
                <h2 class="text-base font-semibold text-foreground">Scheduled tasks</h2>
                <p class="mt-1 text-sm text-muted">Upcoming recurring tasks and their next run times.</p>
            </div>

            <ul v-if="scheduledTasks.length > 0" class="divide-y divide-border">
                <li v-for="task in scheduledTasks" :key="task.id" class="flex flex-col gap-3 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0 flex-1">
                        <h3 class="break-words text-sm font-medium text-foreground">{{ task.name }}</h3>
                        <p class="mt-1 break-words text-xs text-muted">{{ task.subtitle }}</p>
                        <p class="mt-1 text-sm text-muted">
                            {{ task.frequency }}
                            <code class="ml-1 rounded bg-surface-muted px-1.5 py-0.5 text-xs">{{ task.expression }}</code>
                        </p>
                        <p class="mt-1 text-xs text-muted">
                            Next run {{ taskCountdown(task) }} · {{ format(task.nextRunAt) }} {{ task.timezone }}
                        </p>
                    </div>
                    <div class="flex shrink-0 flex-col items-end gap-2">
                        <div class="flex gap-2">
                            <OutlinedButton
                                variant="secondary"
                                size="sm"
                                :disabled="Boolean(processingActions[task.id])"
                                :loading="processingActions[task.id] === 'queue'"
                                @click="queueTask(task.id)"
                            >
                                Queue
                            </OutlinedButton>
                            <OutlinedButton
                                variant="danger"
                                size="sm"
                                :disabled="Boolean(processingActions[task.id])"
                                :loading="processingActions[task.id] === 'run'"
                                @click="requestRunNow(task)"
                            >
                                Run now
                            </OutlinedButton>
                        </div>
                        <p
                            v-if="taskActionFeedback[task.id]"
                            :class="taskActionFeedback[task.id].type === 'error' ? 'text-xs text-danger' : 'text-xs text-success'"
                            :role="taskActionFeedback[task.id].type === 'error' ? 'alert' : 'status'"
                        >
                            {{ taskActionFeedback[task.id].message }}
                        </p>
                    </div>
                </li>
            </ul>
            <p v-else class="rounded-lg border border-dashed border-border p-6 text-center text-sm text-muted">
                No scheduled tasks are configured.
            </p>
        </Card>

        <Card class="space-y-4 rounded-xl border border-border bg-surface">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold text-foreground">Recent activity</h2>
                    <p class="mt-1 text-sm text-muted">Latest tracked job runs.</p>
                </div>
                <Link
                    :href="Routes.audit.backgroundJobs.runs"
                    class="text-sm font-medium text-primary hover:underline focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus"
                >
                    View all jobs
                </Link>
            </div>

            <ul v-if="dashboard.recentRuns.length > 0" class="divide-y divide-border">
                <li v-for="run in dashboard.recentRuns" :key="run.id">
                    <Link
                        :href="Routes.audit.backgroundJobs.show(run.id)"
                        class="flex flex-wrap items-center justify-between gap-3 py-3 hover:bg-surface-hover focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus"
                    >
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-medium text-foreground">{{ jobName(run.job_name) }}</span>
                            <span class="mt-1 block text-xs text-muted">{{ run.queue_label }}</span>
                        </span>
                        <span class="flex items-center gap-3">
                            <Badge :variant="statusVariant(run.status)" size="sm">{{ statusLabel(run.status) }}</Badge>
                            <span class="text-xs tabular-nums text-muted">{{ durationLabel(run.duration_ms) }}</span>
                            <time :datetime="run.queued_at ?? run.created_at" class="min-w-24 text-right text-xs text-muted">
                                {{ timeAgo(run.queued_at ?? run.created_at) ?? format(run.queued_at ?? run.created_at) }}
                            </time>
                        </span>
                    </Link>
                </li>
            </ul>
            <p v-else class="rounded-lg border border-dashed border-border p-6 text-center text-sm text-muted">
                No tracked jobs have been queued yet.
            </p>
        </Card>

        <ConfirmDialog
            ref="runTaskDialog"
            title="Run scheduled task now?"
            :message="`This will immediately execute ${pendingRunTask?.name ?? 'the selected scheduled task'}.`"
            confirm-title="Run now"
            confirm-variant="danger"
            :on-confirm="confirmRunNow"
        />
    </div>
</template>
