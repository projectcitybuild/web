<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Routes } from '../../../../Navigation/Routes'
import Badge from '../../../../Components/Badge.vue'
import Card from '../../../../Components/Card.vue'
import OutlinedButton from '../../../../Components/OutlinedButton.vue'
import PageHeader from '../../../../Components/PageHeader.vue'
import { format } from '../../../../Utilities/DateFormatter'

const props = defineProps<AdminSharedPageProps & AdminInertiaPageProps['audit/BackgroundJobs/Show']>()
const isRetrying = ref(false)

function retryFailedJob(): void {
    if (isRetrying.value) {
        return
    }

    isRetrying.value = true
    router.post(Routes.audit.backgroundJobs.retry(props.run.id), {}, {
        preserveScroll: true,
        onFinish: () => {
            isRetrying.value = false
        },
    })
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

function statusLabel(status: BackgroundJobRunSummary['status']): string {
    return status === 'succeeded' ? 'Successful' : status.charAt(0).toUpperCase() + status.slice(1)
}

function durationLabel(milliseconds: number | null): string {
    if (milliseconds === null) {
        return 'Not available'
    }

    return milliseconds < 1000
        ? `${milliseconds} ms`
        : `${(milliseconds / 1000).toFixed(1)} s`
}
</script>

<template>
    <div class="space-y-6">
        <Head :title="`Background Job: ${run.job_name}`" />

        <PageHeader :title="run.job_name.split('\\').pop() ?? run.job_name" description="Safe lifecycle details for this background job run.">
            <template #actions>
                <div class="flex flex-wrap gap-2">
                    <OutlinedButton
                        v-if="can_retry"
                        variant="primary"
                        :disabled="isRetrying"
                        @click="retryFailedJob"
                    >
                        {{ isRetrying ? 'Requeuing...' : 'Requeue job' }}
                    </OutlinedButton>
                    <Link
                        :href="Routes.audit.backgroundJobs.throughput"
                        class="inline-flex min-h-10 items-center rounded-md border border-border-strong px-3 text-sm font-medium text-foreground hover:bg-surface-hover focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus"
                    >
                        Back to throughput
                    </Link>
                </div>
            </template>
        </PageHeader>

        <Card class="space-y-6 rounded-xl border border-border bg-surface">
            <header class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm text-muted">Tracking ID</p>
                    <p class="mt-1 break-all font-mono text-sm text-foreground">{{ run.id }}</p>
                </div>
                <Badge :variant="statusVariant(run.status)">{{ statusLabel(run.status) }}</Badge>
            </header>

            <dl class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <dt class="text-sm text-muted">Job name</dt>
                    <dd class="mt-1 break-all text-sm font-medium text-foreground">{{ run.job_name }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-muted">Queue</dt>
                    <dd class="mt-1 text-sm font-medium text-foreground">{{ run.queue_label }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-muted">Attempts</dt>
                    <dd class="mt-1 text-sm font-medium text-foreground">
                        {{ run.attempts }}{{ run.max_attempts === null ? '' : ` of ${run.max_attempts}` }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm text-muted">Duration</dt>
                    <dd class="mt-1 text-sm font-medium text-foreground">{{ durationLabel(run.duration_ms) }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-muted">Queued</dt>
                    <dd class="mt-1 text-sm font-medium text-foreground">{{ format(run.queued_at) ?? 'Not available' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-muted">Started</dt>
                    <dd class="mt-1 text-sm font-medium text-foreground">{{ format(run.started_at) ?? 'Not started' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-muted">Completed</dt>
                    <dd class="mt-1 text-sm font-medium text-foreground">{{ format(run.completed_at) ?? 'Not completed' }}</dd>
                </div>
            </dl>

            <section v-if="Object.keys(run.metadata).length > 0" class="space-y-2 border-t border-border pt-5">
                <h2 class="text-base font-semibold text-foreground">Safe metadata</h2>
                <dl class="grid gap-3 sm:grid-cols-2">
                    <div v-for="(value, key) in run.metadata" :key="key" class="rounded-lg bg-surface-muted p-3">
                        <dt class="text-xs text-muted">{{ key }}</dt>
                        <dd class="mt-1 break-all text-sm font-medium text-foreground">{{ value }}</dd>
                    </div>
                </dl>
            </section>

            <section v-if="run.status === 'failed'" class="space-y-2 border-t border-border pt-5">
                <h2 class="text-base font-semibold text-danger">Failure</h2>
                <p class="text-sm text-muted">{{ run.error_message ?? 'The job failed.' }}</p>
                <p v-if="run.exception_class" class="break-all font-mono text-xs text-muted">{{ run.exception_class }}</p>
            </section>
        </Card>
    </div>
</template>
