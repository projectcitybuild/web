<script setup lang="ts">
import { BuilderRankApplication, BuilderRankApplicationStatus } from '../../../../manage/Data/BuilderRankApplication'
import { format } from '../../../../manage/Utilities/DateFormatter'
import Card from '../../../../shared/Components/Card.vue'

interface Props {
    application: BuilderRankApplication,
}
defineProps<Props>()
</script>

<template>
    <div>
        <Card
            padding="none"
            v-if="application.status === BuilderRankApplicationStatus.denied"
            class="border-red-500"
        >
            <div class="rounded-t-md bg-red-500 p-4">
                <h2 class="text-base font-semibold text-white">Decision: Denied</h2>
            </div>

            <div class="p-4 italic">
                {{ application.denied_reason }}
            </div>

            <div class="p-4 border-t border-border">
                <dl class="flex items-center justify-between gap-2">
                    <dt class="text-sm text-muted">Decided At</dt>
                    <dd class="text-sm text-foreground">
                        {{ format(application.closed_at) }}
                    </dd>
                </dl>
            </div>
        </Card>

        <Card
            padding="none"
            v-if="application.status === BuilderRankApplicationStatus.approved"
            class="border-green-500"
        >
            <div class="rounded-t-md bg-green-500 p-4">
                <h2 class="text-base font-semibold text-white">Decision: Approved</h2>
            </div>

            <div class="p-4 border-t border-border">
                <dl class="flex items-center justify-between gap-2">
                    <dt class="text-sm text-muted">Decided At</dt>
                    <dd class="text-sm text-foreground">
                        {{ format(application.closed_at) }}
                    </dd>
                </dl>
            </div>
        </Card>
    </div>
</template>
