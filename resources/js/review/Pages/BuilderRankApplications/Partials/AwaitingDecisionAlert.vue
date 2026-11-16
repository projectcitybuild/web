<script setup lang="ts">
import Card from '../../../../shared/Components/Card.vue'
import SvgIcon from '../../../../manage/Components/SvgIcon.vue'
import { Icons } from '../../../../manage/Icons'
</script>

<template>
    <Card padding="none" class="border border-red-500 p-4">
        <h2 class="mb-3 flex items-center gap-2 text-base font-semibold text-danger">
            <SvgIcon :svg="Icons.hourglass" class="size-6" aria-hidden="true" />
            Awaiting Decision
        </h2>

        <span class="text-sm leading-6">
            <strong>Please discuss the submission in Discord.</strong><br />
            Once a decision has been reached, click the approve or deny button below to finish review of this application.
        </span>
    </Card>
</template>
