<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

interface Props {
    href: string,
}

defineProps<Props>()
</script>

<template>
    <Link :href="href" class="flex flex-row items-center gap-2 text-sm text-gray-500">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
             class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75 3 12m0 0 3.75-3.75M3 12h18"/>
        </svg>
        Go back
    </Link>
</template>
