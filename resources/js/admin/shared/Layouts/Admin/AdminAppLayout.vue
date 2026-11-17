<script setup lang="ts">
import { computed } from 'vue'
import AdminLayout from './AdminLayout.vue'
import useAdminNavigation, { type AdminNavigationEntry } from './useAdminNavigation'
import ManageSideBar from '../../../manage/Layouts/Root/Partials/SideBar.vue'
import { sidebarMenu as manageNavigation } from '../../../manage/Navigation/Sidebar'
import ReviewSideBar from '../../../review/Layouts/Root/Partials/SideBar.vue'
import { sidebarMenu as reviewNavigation } from '../../../review/Navigation/Sidebar'
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

    return []
})
</script>

<template>
    <AdminLayout :area-badges="areaBadges" :navigation="navigation">
        <template #side-bar>
            <ManageSideBar v-if="currentArea === 'manage'" />
            <ReviewSideBar v-else-if="currentArea === 'review'" />
        </template>

        <slot />
    </AdminLayout>
</template>
