<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import Badge from '../../shared/Components/Badge.vue'
import Card from '../../shared/Components/Card.vue'
import PageHeader from '../../shared/Components/PageHeader.vue'
import SvgIcon from '../../manage/Components/SvgIcon.vue'
import usePermissions from '../../manage/Composables/usePermissions'
import { Icons } from '../../manage/Icons'
import { sidebarMenu } from '../Navigation/Sidebar'

const page = usePage()
const { can } = usePermissions()
const queues = computed(() => sidebarMenu.filter(item =>
    item.route !== '/review' && (!item.permission || can(item.permission))
))

function countFor(item) {
    return item.countKey ? Number(page.props[item.countKey] ?? 0) : 0
}
</script>

<template>
    <Head title="Review" />

    <div class="space-y-6">
        <PageHeader title="Review" />

        <section
            v-if="queues.length > 0"
            aria-label="Review queues"
            class="grid grid-cols-1 gap-4 sm:grid-cols-2"
        >
            <Card v-for="item in queues" :key="item.route" padding="none">
                <Link
                    :href="item.route"
                    class="flex min-h-24 items-center justify-between gap-4 rounded-[var(--radius-lg)] p-4 text-foreground transition-colors hover:bg-surface-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus focus-visible:ring-inset"
                >
                    <span class="flex min-w-0 items-center gap-3">
                        <SvgIcon :svg="Icons[item.icon]" class="size-5 shrink-0 text-muted" />
                        <span class="truncate font-semibold">{{ item.title }}</span>
                    </span>
                    <Badge :variant="countFor(item) > 0 ? 'warning' : 'default'">
                        {{ countFor(item) }} pending
                    </Badge>
                </Link>
            </Card>
        </section>

        <Card v-else class="text-sm text-muted">
            No review queues are available to your account.
        </Card>
    </div>
</template>
