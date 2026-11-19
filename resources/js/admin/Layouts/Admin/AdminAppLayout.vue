<script setup lang="ts">
import { computed } from 'vue'
import AdminLayout from './AdminLayout.vue'
import useAdminNavigation, { type AdminNavigationEntry } from '../../Navigation/useAdminNavigation'
import { sidebarMenu as manageNavigation } from '../../Areas/Manage/Sidebar.js'
import { sidebarMenu as reviewNavigation } from '../../Areas/Review/Sidebar.js'
import { sidebarMenu as auditingNavigation } from '../../Areas/Audit/Sidebar.js'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const { currentArea } = useAdminNavigation()

const areaBadges = computed(() => {
    const pendingCount = Number(page.props.pending_appeals ?? 0) + Number(page.props.pending_build_rank_apps ?? 0)

    return { review: pendingCount > 99 ? '99+' : pendingCount }
})

const navigation = computed<AdminNavigationEntry[]>(() => {
    if (currentArea.value === 'manage') {
        return manageNavigation
    }
    if (currentArea.value === 'review') {
        return reviewNavigation
    }
    if (currentArea.value === 'audit') {
        return auditingNavigation
    }
    return []
})
</script>

<template>
    <AdminLayout :area-badges="areaBadges" :navigation="navigation">
        <slot />
    </AdminLayout>
</template>
