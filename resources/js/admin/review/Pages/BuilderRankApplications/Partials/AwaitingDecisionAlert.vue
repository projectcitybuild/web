<script setup lang="ts">
import Card from '../../../../shared/Components/Card.vue'
import SvgIcon from '../../../../manage/Components/SvgIcon.vue'
import { Icons } from '../../../../manage/Icons'
</script>

<template>
    <Card padding="none" class="rounded-xl border border-warning bg-warning-soft p-4">
        <h2 class="mb-2 flex items-center gap-2 text-base font-semibold text-warning">
            <SvgIcon :svg="Icons.hourglass" class="size-6" aria-hidden="true" />
            Review in progress
        </h2>

        <p class="text-sm leading-6 text-foreground">
            Discuss the submission with the council, then record the final decision in the review panel.
        </p>
    </Card>
</template>
