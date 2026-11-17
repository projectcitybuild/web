<script setup lang="ts">
import { nextTick, onBeforeUnmount, provide, ref, watch } from 'vue'
import AdminAreaRail from './components/AdminAreaRail.vue'
import AdminHeader from './components/AdminHeader.vue'
import AdminSidebar from './components/AdminSidebar.vue'
import useTheme from './useTheme'
import type { AdminNavigationEntry } from './useAdminNavigation'

const props = withDefaults(defineProps<{
    areaBadges?: Record<string, number | string>
    navigation?: AdminNavigationEntry[]
}>(), {
    areaBadges: () => ({}),
    navigation: () => [],
})

const { theme } = useTheme()
const pageTitle = ref('')
provide('adminPageTitle', pageTitle)
const sidebarOpen = ref(false)
const sidebarCollapsed = ref(false)
const sidebar = ref<InstanceType<typeof AdminSidebar> | null>(null)
let previousBodyOverflow = ''

function closeSidebar(): void {
    sidebarOpen.value = false
    nextTick(() => document.getElementById('admin-menu-toggle')?.focus())
}

function openSidebar(): void {
    sidebarOpen.value = true
    nextTick(() => sidebar.value?.focusFirst())
}

function toggleDesktopSidebar(): void {
    sidebarCollapsed.value = !sidebarCollapsed.value
}

function onKeydown(event: KeyboardEvent): void {
    if (!sidebarOpen.value) {
        return
    }

    if (event.key === 'Escape') {
        closeSidebar()
        return
    }

    if (event.key !== 'Tab') {
        return
    }

    const focusableElements = sidebar.value?.$el.querySelectorAll<HTMLElement>(
        'button:not([disabled]), a[href]:not([tabindex="-1"])'
    )
    if (!focusableElements?.length) {
        return
    }

    const first = focusableElements[0]
    const last = focusableElements[focusableElements.length - 1]
    if (event.shiftKey && document.activeElement === first) {
        event.preventDefault()
        last.focus()
    } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault()
        first.focus()
    }
}

watch(sidebarOpen, open => {
    if (open) {
        previousBodyOverflow = document.body.style.overflow
        document.body.style.overflow = 'hidden'
        document.addEventListener('keydown', onKeydown)
    } else {
        document.body.style.overflow = previousBodyOverflow
        document.removeEventListener('keydown', onKeydown)
    }
})

onBeforeUnmount(() => {
    document.body.style.overflow = previousBodyOverflow
    document.removeEventListener('keydown', onKeydown)
})
</script>

<template>
    <div class="min-h-screen bg-background text-foreground" :data-theme="theme">
        <AdminAreaRail :area-badges="props.areaBadges" :collapsed="sidebarCollapsed" />

        <AdminHeader
            :sidebar-collapsed="sidebarCollapsed"
            :on-sidebar-toggle="toggleDesktopSidebar"
            :menu-open="sidebarOpen"
            :on-menu-click="openSidebar"
            :navigation="props.navigation"
        />

        <button
            v-if="sidebarOpen"
            type="button"
            class="fixed inset-0 z-40 bg-black/50 md:hidden"
            aria-label="Close navigation menu"
            @click="closeSidebar"
        />

        <AdminSidebar ref="sidebar" :open="sidebarOpen" :collapsed="sidebarCollapsed" :area-badges="props.areaBadges" @close="closeSidebar">
            <slot name="side-bar" />
        </AdminSidebar>

        <main
            class="min-h-screen min-w-0 px-4 pb-8 pt-20 transition-[margin] duration-200 motion-reduce:transition-none sm:px-6 md:px-6"
            :class="sidebarCollapsed ? 'md:ml-14' : 'md:ml-[18.5rem]'"
        >
            <slot />
        </main>
    </div>
</template>
