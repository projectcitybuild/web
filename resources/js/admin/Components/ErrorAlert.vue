<script setup lang="ts">
import SvgIcon from './SvgIcon.vue'
import { Icons } from '../Components/SvgIcon.vue'

interface Props {
    errors: object,
}

defineProps<Props>()
</script>

<template>
    <div class="flex rounded-lg bg-danger-soft p-4 text-sm text-danger" role="alert">
        <SvgIcon :svg="Icons.info" class="me-3 mt-[2px] inline size-4 shrink-0" aria-hidden="true" />
        <span class="sr-only">Error</span>
        <div>
            <span class="font-medium">Ensure that these requirements are met:</span>
            <ul class="mt-1.5 list-disc list-inside">
                <li v-for="error in errors">{{ error }}</li>
            </ul>
        </div>
    </div>
</template>
