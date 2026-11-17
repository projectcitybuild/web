<script setup lang="ts">
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import Badge from '../../../Components/Badge.vue'
import { isAdminPathActive } from '../useAdminNavigation'

const props = defineProps<{
    href: string
    label: string
    badge?: number | string
    exact?: boolean
}>()

const page = usePage()
const active = computed(() => isAdminPathActive(page.url, props.href, props.exact))
</script>

<template>
    <Link
        :href="href"
        :aria-current="active ? 'page' : undefined"
        :class="[
            'flex min-h-10 items-center gap-3 rounded-lg px-3 text-xs transition-colors duration-150 focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus focus-visible:ring-offset-2 focus-visible:ring-offset-surface-muted motion-reduce:transition-none',
            active ? 'bg-surface-muted font-semibold text-foreground' : 'text-muted hover:bg-surface-hover hover:text-foreground',
        ]"
    >
        <span v-if="$slots.icon" class="flex size-5 shrink-0 items-center justify-center" aria-hidden="true">
            <slot name="icon" />
        </span>
        <span class="min-w-0 flex-1 truncate">{{ label }}</span>
        <Badge v-if="badge && badge !== 0" class="shrink-0" variant="danger" tone="solid" size="sm">
            {{ badge }}
        </Badge>
    </Link>
</template>
