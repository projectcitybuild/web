<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

interface Breadcrumb {
    label: string,
    href?: string,
}

defineProps<{
    items: Breadcrumb[],
}>()
</script>

<template>
    <ol class="flex flex-wrap items-center gap-x-2 gap-y-1">
        <li v-for="(item, index) in items" :key="`${item.label}-${index}`" class="flex min-w-0 items-center gap-2">
            <span v-if="index > 0" aria-hidden="true">/</span>
            <Link
                v-if="item.href"
                :href="item.href"
                class="max-w-full truncate hover:text-foreground"
            >
                {{ item.label }}
            </Link>
            <span
                v-else
                :aria-current="index === items.length - 1 ? 'page' : undefined"
                class="max-w-full truncate text-foreground"
            >
                {{ item.label }}
            </span>
        </li>
    </ol>
</template>
