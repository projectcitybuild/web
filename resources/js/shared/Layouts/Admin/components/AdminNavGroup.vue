<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { isAdminPathActive } from '../useAdminNavigation'
import AdminNavLink from './AdminNavLink.vue'

interface ChildLink {
    title: string
    route: string
    icon: {
        fill?: string
        paths: { d: string; strokeLineCap?: 'butt' | 'round' | 'square'; strokeLineJoin?: 'miter' | 'round' | 'bevel' }[]
    }
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
            class="flex min-h-9 w-full items-center gap-2.5 rounded-md px-2.5 text-left text-xs tracking-wide text-muted transition-colors hover:bg-surface-hover hover:text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus"
            :aria-expanded="expanded"
            @click="expanded = !expanded"
        >
            <span class="min-w-0 flex-1 truncate">{{ title }}</span>
            <svg aria-hidden="true" class="size-4 transition-transform" :class="expanded ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
            </svg>
        </button>
        <ul v-show="expanded" class="mt-1.5 space-y-0.5 border-border pl-2">
            <li v-for="child in children" :key="child.route">
                <AdminNavLink :href="child.route" :label="child.title">
                    <template #icon>
                        <slot name="child-icon" :child="child" />
                    </template>
                </AdminNavLink>
            </li>
        </ul>
    </li>
</template>
