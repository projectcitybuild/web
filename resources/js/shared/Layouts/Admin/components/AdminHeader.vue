<script setup lang="ts">
import { computed, inject, ref } from 'vue'
import type { Ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import logo from '../../../../../images/logo-alt.png'
import ThemeToggle from './ThemeToggle.vue'
import useAdminNavigation, { adminAreas, buildAdminBreadcrumbs } from '../useAdminNavigation'
import type { AdminNavigationEntry } from '../useAdminNavigation'

const props = defineProps<{
    sidebarCollapsed: boolean
    onSidebarToggle: () => void
    menuOpen: boolean
    onMenuClick: () => void
    navigation: AdminNavigationEntry[]
}>()

const page = usePage()
const { currentArea } = useAdminNavigation()
const pageTitle = inject<Ref<string>>('adminPageTitle', ref(''))
const area = computed(() => adminAreas.find(item => item.id === currentArea.value) ?? null)
const breadcrumbs = computed(() => buildAdminBreadcrumbs(page.url, area.value, props.navigation, pageTitle.value))
</script>

<template>
    <header
        class="fixed inset-x-0 top-0 z-40 flex h-14 items-center justify-between border-b border-border bg-surface px-4 transition-[left] duration-200 motion-reduce:transition-none sm:px-6"
        :class="sidebarCollapsed ? 'md:left-11' : 'md:left-[15.75rem]'"
    >
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

            <a href="/" class="hidden shrink-0 items-center sm:flex md:hidden" aria-label="Project City Build home">
                <img :src="logo" alt="Project City Build" class="h-9 w-auto" />
            </a>

            <button
                type="button"
                class="hidden size-8 shrink-0 items-center justify-center rounded-md text-muted hover:bg-surface-hover hover:text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus md:inline-flex"
                :aria-label="sidebarCollapsed ? 'Expand navigation' : 'Collapse navigation'"
                :title="sidebarCollapsed ? 'Expand navigation' : 'Collapse navigation'"
                aria-controls="admin-sidebar"
                :aria-expanded="!sidebarCollapsed"
                @click="onSidebarToggle"
            >
                <svg aria-hidden="true" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <rect x="3.5" y="4.5" width="17" height="15" rx="1.5" />
                    <path stroke-linecap="round" d="M9 5v14m4-10h4m-4 4h4" />
                </svg>
            </button>

            <nav v-if="breadcrumbs.length" aria-label="Breadcrumb" class="min-w-0">
                <ol class="flex min-w-0 items-center gap-2 text-sm">
                    <template v-for="(breadcrumb, index) in breadcrumbs" :key="`${breadcrumb.label}-${index}`">
                        <li v-if="index > 0" aria-hidden="true" class="flex items-center text-subtle">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                            </svg>
                        </li>
                        <li class="min-w-0 truncate">
                            <Link
                                v-if="breadcrumb.href"
                                :href="breadcrumb.href"
                                class="whitespace-nowrap text-muted hover:text-foreground"
                            >
                                {{ breadcrumb.label }}
                            </Link>
                            <span
                                v-else
                                :aria-current="breadcrumb.current ? 'page' : undefined"
                                :class="breadcrumb.current ? 'font-semibold text-foreground' : 'font-normal text-muted'"
                                class="truncate"
                            >
                                {{ breadcrumb.label }}
                            </span>
                        </li>
                    </template>
                </ol>
            </nav>
        </div>

        <div class="flex shrink-0 items-center">
            <ThemeToggle />
        </div>
    </header>
</template>
