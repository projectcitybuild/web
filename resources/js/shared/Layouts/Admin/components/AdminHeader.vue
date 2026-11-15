<script setup lang="ts">
import logo from '../../../../../images/logo-alt.png'
import Badge from '../../../../manage/Components/Badge.vue'
import ThemeToggle from './ThemeToggle.vue'
import useAdminNavigation, { adminAreas } from '../useAdminNavigation'

defineProps<{
    areaBadges: Record<string, number | string>
    menuOpen: boolean
    onMenuClick: () => void
}>()

const { currentArea } = useAdminNavigation()
</script>

<template>
    <header class="fixed inset-x-0 top-0 z-40 flex h-16 items-center justify-between border-b border-border bg-surface px-4 sm:px-6">
        <div class="flex min-w-0 items-center gap-3">
            <button
                id="admin-menu-toggle"
                type="button"
                class="inline-flex size-10 shrink-0 items-center justify-center rounded-md text-muted hover:bg-surface-hover hover:text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus md:hidden"
                aria-label="Open navigation menu"
                aria-controls="admin-sidebar"
                :aria-expanded="menuOpen"
                @click="onMenuClick"
            >
                <svg aria-hidden="true" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <a href="/" class="hidden shrink-0 items-center sm:flex" aria-label="Project City Build home">
                <img :src="logo" alt="Project City Build" class="h-9 w-auto" />
            </a>
        </div>

        <nav aria-label="Administrative area" class="flex min-w-0 items-center gap-1">
            <a
                v-for="area in adminAreas"
                :key="area.id"
                :href="area.href"
                :aria-current="currentArea === area.id ? 'page' : undefined"
                :class="[
                    'inline-flex min-h-10 items-center gap-2 rounded-md px-3 text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus',
                    currentArea === area.id ? 'bg-primary text-primary-foreground' : 'text-muted hover:bg-surface-hover hover:text-foreground',
                ]"
            >
                {{ area.label }}
                <Badge v-if="areaBadges[area.id]" :count="areaBadges[area.id]" />
            </a>
        </nav>

        <div class="flex shrink-0 items-center">
            <ThemeToggle />
        </div>
    </header>
</template>
