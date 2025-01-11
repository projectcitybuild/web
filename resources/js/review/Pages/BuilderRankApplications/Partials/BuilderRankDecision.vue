<script setup lang="ts">
import { BuilderRankApplication, BuilderRankApplicationStatus } from '../../../../manage/Data/BuilderRankApplication'
import { format } from '../../../../manage/Utilities/DateFormatter'
import Card from '../../../../manage/Components/Card.vue'

interface Props {
    application: BuilderRankApplication,
}
defineProps<Props>()
</script>

<template>
    <div>
        <Card
            v-if="application.status === BuilderRankApplicationStatus.denied"
            class="border-red-500"
        >
            <div class="p-4 rounded-t-md bg-red-500">
                <h2 class="font-bold text-white">Decision: Denied</h2>
            </div>

            <div class="p-4 italic">
                {{ application.denied_reason }}
            </div>

            <div class="p-4 border-t border-gray-200">
                <dl class="flex items-center justify-between gap-2">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Decided At</dt>
                    <dd class="text-sm text-gray-900 dark:text-white">
                        {{ format(application.closed_at) }}
                    </dd>
                </dl>
            </div>
        </Card>

        <Card
            v-if="application.status === BuilderRankApplicationStatus.approved"
            class="border-green-500"
        >
            <div class="p-4 rounded-t-md bg-green-500">
                <h2 class="font-bold text-white">Decision: Approved</h2>
            </div>

            <div class="p-4 border-t border-gray-200">
                <dl class="flex items-center justify-between gap-2">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Decided At</dt>
                    <dd class="text-sm text-gray-900 dark:text-white">
                        {{ format(application.closed_at) }}
                    </dd>
                </dl>
            </div>
        </Card>
    </div>
</template>
