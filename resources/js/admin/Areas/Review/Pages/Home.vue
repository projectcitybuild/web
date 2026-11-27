<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import Card from '../../../Components/Card.vue'
import PageHeader from '../../../Components/PageHeader.vue'
import SvgIcon from '../../../Components/SvgIcon.vue'
import usePermissions from '../../../Composables/usePermissions'
import { Icons } from '../../../Components/SvgIcon.vue'
import type { SidebarChild } from '../../../Navigation/Sidebar'
import { sidebarMenu } from '../Sidebar.js'

const page = usePage()
const { can } = usePermissions()
const queues = computed(() => sidebarMenu
    .flatMap(section => section.children)
    .filter(item => item.route !== '/review' && (!item.permission || can(item.permission)))
)

const metricLabels = {
    pending_appeals: 'Ban Appeals',
    pending_build_rank_apps: 'Applications',
    pending_player_reports: 'Player Reports',
}

type ReviewMetric = SidebarChild & { countKey: keyof typeof metricLabels }

const metrics = computed(() => queues.value.filter(
    (item): item is ReviewMetric => item.countKey !== undefined,
))

function countFor(item: SidebarChild): number {
    return item.countKey ? Number(page.props[item.countKey] ?? 0) : 0
}
</script>

<template>
    <Head title="Overview" />

    <div class="space-y-6">
        <PageHeader title="Overview" description="Your review workspace at a glance." class="border-b border-border pb-6" />

        <section
            v-if="metrics.length > 0"
            aria-label="Review queues"
            class="grid grid-cols-1 gap-4 sm:grid-cols-2"
        >
            <Card v-for="item in metrics" :key="item.route" padding="none" class="overflow-hidden rounded-xl border border-border bg-surface-muted">
                <Link
                    :href="item.route"
                    class="flex min-h-40 flex-col items-start gap-3 rounded-xl p-4 text-foreground transition-colors hover:bg-surface-hover focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus focus-visible:ring-inset"
                >
                    <SvgIcon :svg="Icons[item.icon]" class="size-5 shrink-0 text-muted" aria-hidden="true" />
                    <span class="text-3xl font-semibold leading-none text-foreground">{{ countFor(item) }}</span>
                    <span class="text-sm font-medium text-muted">{{ metricLabels[item.countKey] }}</span>
                </Link>
            </Card>
        </section>

        <Card v-else class="text-sm text-muted">
            No review queues are available to your account.
        </Card>
    </div>
</template>
