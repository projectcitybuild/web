<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Card from '../../shared/Components/Card.vue'
import PageHeader from '../../shared/Components/PageHeader.vue'
import MinecraftAvatar from '../Components/MinecraftAvatar.vue'

const props = defineProps({
    recentActivities: {
        type: Array,
        default: null,
    },
    activityUrl: {
        type: String,
        default: null,
    },
})

</script>

<template>
    <Head title="Management" />

    <div class="space-y-6">
        <PageHeader title="Management" description="Manage the Project City Build platform." />

        <section v-if="props.recentActivities" aria-label="Recent activity" class="space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-base font-semibold text-foreground">Recent activity</h2>
                <Link
                    v-if="props.activityUrl"
                    :href="props.activityUrl"
                    class="text-sm font-medium text-foreground underline decoration-border underline-offset-4 hover:text-muted focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus"
                >
                    View full audit log
                </Link>
            </div>

            <Card padding="md">
                <div v-if="props.recentActivities.length > 0" class="divide-y divide-border">
                    <div
                        v-for="activity in props.recentActivities"
                        :key="activity.id"
                        class="grid grid-cols-1 gap-1 px-4 py-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center sm:gap-4"
                    >
                        <div class="flex min-w-0 items-center gap-3">
                            <MinecraftAvatar
                                v-if="activity.avatarUuid"
                                :uuid="activity.avatarUuid"
                                :size="32"
                                class="shrink-0 overflow-hidden rounded-full"
                            />
                            <p class="min-w-0 break-words text-sm text-muted">
                                <span class="font-medium text-foreground">{{ activity.actor }}</span>
                                {{ activity.action }}
                            </p>
                        </div>
                        <time class="text-xs text-muted sm:whitespace-nowrap">{{ activity.when }}</time>
                    </div>
                </div>
                <p v-else class="p-4 text-sm text-muted">No recent activity.</p>
            </Card>
        </section>
    </div>
</template>
