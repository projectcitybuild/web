<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { isAdminPathActive } from '../useAdminNavigation'
import AdminNavLink from './AdminNavLink.vue'

interface ChildLink {
    title: string
    route: string
}

const props = defineProps<{
    title: string
    children: ChildLink[]
}>()

const page = usePage()
const activeChild = computed(() => props.children.some(child => isAdminPathActive(page.url, child.route)))
const expanded = ref(activeChild.value)

watch(activeChild, active => {
    if (active) {
        expanded.value = true
    }
})
</script>

<template>
    <li>
        <button
            type="button"
            class="flex min-h-10 w-full items-center gap-3 rounded-md px-3 text-left text-sm text-muted transition-colors hover:bg-surface-hover hover:text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus"
            :aria-expanded="expanded"
            @click="expanded = !expanded"
        >
            <span class="flex size-5 shrink-0 items-center justify-center" aria-hidden="true"><slot name="icon" /></span>
            <span class="min-w-0 flex-1 truncate">{{ title }}</span>
            <svg aria-hidden="true" class="size-4 transition-transform" :class="expanded ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
            </svg>
        </button>
        <ul v-show="expanded" class="ml-4 mt-1 space-y-1 border-l border-border pl-2">
            <li v-for="child in children" :key="child.route">
                <AdminNavLink :href="child.route" :label="child.title" />
            </li>
        </ul>
    </li>
</template>
