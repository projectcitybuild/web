<script setup lang="ts">
import { Routes } from '../../../../Routes'
import { computed, ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AdminAccountMenu from './AdminAccountMenu.vue'
import AdminNavGroup from './AdminNavGroup.vue'
import AdminNavLink from './AdminNavLink.vue'
import SvgIcon from '../../../../manage/Components/SvgIcon.vue'
import { Icons } from '../../../../manage/Icons'
import usePermissions from '../../../../manage/Composables/usePermissions'
import { filterSidebar } from '../../../../manage/Navigation/FilterSidebar'
import { sidebarMenu as manageMenu } from '../../../../manage/Navigation/Sidebar'
import { sidebarMenu as reviewMenu } from '../../../../review/Navigation/Sidebar'
import { sidebarMenu as auditingMenu } from '../../../../audit/Navigation/Sidebar.js'
import useAdminNavigation, { adminAreas } from '../useAdminNavigation'

const props = defineProps<{
    open: boolean
    collapsed: boolean
    areaBadges: Record<string, number | string>
}>()
const emit = defineEmits<{ close: [] }>()
const { currentArea } = useAdminNavigation()
const page = usePage()
const { can } = usePermissions()

const sidebar = ref<HTMLElement | null>(null)
const visibleManageMenu = computed(() => filterSidebar(manageMenu, can))
const visibleReviewMenu = computed(() => reviewMenu.filter(item =>
    !item.permission || can(item.permission)
))

function countFor(item: typeof reviewMenu[number]): number {
    return item.countKey ? Number(page.props[item.countKey] ?? 0) : 0
}

const activeAreaMenu = computed(() => {
    if (currentArea.value === 'review') {
        return visibleReviewMenu.value.map(item => ({
            ...item,
            badge: countFor(item),
            exact: item.route === '/review',
        }))
    }

    if (currentArea.value === 'audit') {
        return auditingMenu.map(item => ({
            ...item,
            badge: undefined,
            exact: item.route === '/audit',
        }))
    }

    return []
})

function focusFirst(): void {
    sidebar.value?.querySelector<HTMLElement>('button, a[href]')?.focus()
}

function closeAfterNavigation(event: MouseEvent): void {
    if (event.target instanceof Element && event.target.closest('a[href]')) {
        emit('close')
    }
}

defineExpose({ focusFirst })
</script>

<template>
    <aside
        id="admin-sidebar"
        ref="sidebar"
        class="fixed bottom-0 left-0 top-14 z-50 flex w-64 flex-col w-90 border-r border-border bg-linear-to-b from-surface-muted to-surface transition-[width,transform] duration-200 ease-out motion-reduce:transition-none md:left-14 md:top-0 md:z-30 md:translate-x-0"
        :class="[
            open ? 'visible translate-x-0' : 'invisible -translate-x-full',
            collapsed ? 'md:invisible md:w-0 md:overflow-hidden md:border-r-0' : 'md:visible md:w-60',
        ]"
        :role="open ? 'dialog' : undefined"
        :aria-modal="open ? 'true' : undefined"
        aria-label="Administrative navigation"
        @click="closeAfterNavigation"
    >
        <button
            type="button"
            class="absolute right-3 top-3 inline-flex size-10 items-center justify-center rounded-md text-muted hover:bg-surface-hover focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus md:hidden"
            aria-label="Close navigation menu"
            @click.stop="$emit('close')"
        >
            <SvgIcon :svg="Icons.close" class="size-5" aria-hidden="true" />
        </button>

        <div class="min-h-0 flex-1 overflow-y-auto py-16 md:py-0">
            <div class="hidden h-14 items-center border-b border-border px-2 md:flex">

            </div>
            <div class="px-3 mt-6">
                <nav aria-label="Administrative areas" class="mb-4 border-b border-border pb-4 md:hidden">
                    <p class="mb-2 px-3 text-[11px] font-semibold tracking-wide text-subtle">Areas</p>

                    <ul class="space-y-1">
                        <li v-for="area in adminAreas" :key="area.id">
                            <Link
                                :href="area.href"
                                :aria-current="currentArea === area.id ? 'page' : undefined"
                                :class="[
                                    'flex min-h-10 items-center justify-between rounded-md px-3 text-sm font-medium transition-colors focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus',
                                    currentArea === area.id ? 'bg-primary-soft text-foreground' : 'text-muted hover:bg-surface-hover hover:text-foreground',
                                ]"
                            >
                                {{ area.label }}
                                <span v-if="props.areaBadges[area.id]" class="rounded-full bg-danger-soft px-2 py-0.5 text-xs font-medium text-danger">
                                    {{ props.areaBadges[area.id] }}
                                </span>
                            </Link>
                        </li>
                    </ul>
                </nav>
                <nav aria-label="Main navigation">
                    <ul v-if="currentArea === 'manage'" class="mt-4 space-y-1">
                        <li>
                            <AdminNavLink :href="Routes.manage.overview" label="Overview" exact>
                                <template #icon>
                                    <SvgIcon :svg="Icons.dashboard" class="size-5" aria-hidden="true" />
                                </template>
                            </AdminNavLink>
                        </li>
                        <AdminNavGroup
                            v-for="section in visibleManageMenu"
                            :key="section.title"
                            :title="section.title"
                            :children="section.children"
                        >
                            <template #child-icon="{ child }">
                                <SvgIcon :svg="child.icon" class="size-4" />
                            </template>
                        </AdminNavGroup>
                    </ul>
                    <ul v-else-if="currentArea === 'review' || currentArea === 'audit'" class="mt-4 space-y-1">
                        <li v-for="item in activeAreaMenu" :key="item.route">
                            <AdminNavLink
                                :href="item.route"
                                :label="item.title"
                                :badge="item.badge"
                                :exact="item.exact"
                            >
                                <template #icon>
                                    <SvgIcon :svg="Icons[item.icon]" class="size-[18px]" aria-hidden="true" />
                                </template>
                            </AdminNavLink>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <div v-if="!collapsed || open" class="shrink-0 border-t border-border px-3 py-3">
            <AdminAccountMenu />
        </div>
    </aside>
</template>
