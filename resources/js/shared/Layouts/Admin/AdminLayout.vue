<script setup lang="ts">
import { nextTick, onBeforeUnmount, ref, watch } from 'vue'
import AdminHeader from './components/AdminHeader.vue'
import AdminSidebar from './components/AdminSidebar.vue'
import useTheme from './useTheme'

const props = withDefaults(defineProps<{
    areaBadges?: Record<string, number | string>
}>(), {
    areaBadges: () => ({}),
})

const { theme } = useTheme()
const sidebarOpen = ref(false)
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

function onKeydown(event: KeyboardEvent): void {
    if (!sidebarOpen.value) {
        return
    }

    if (event.key === 'Escape') {
        closeSidebar()
        menuButton.value?.focus()
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
        <AdminHeader
            :area-badges="props.areaBadges"
            :menu-open="sidebarOpen"
            :on-menu-click="openSidebar"
        />

        <button
            v-if="sidebarOpen"
            type="button"
            class="fixed inset-0 z-40 bg-black/50 md:hidden"
            aria-label="Close navigation menu"
            @click="closeSidebar"
        />

        <AdminSidebar ref="sidebar" :open="sidebarOpen" @close="closeSidebar">
            <slot name="side-bar" />
        </AdminSidebar>

        <main class="mx-auto min-h-screen max-w-screen-xl px-4 pb-8 pt-20 sm:px-6 md:ml-64 md:px-8">
            <slot />
        </main>
    </div>
</template>
