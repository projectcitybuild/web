<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import SideBar from './Partials/SideBar.vue'
import AdminLayout from '../../../shared/Layouts/Admin/AdminLayout.vue'
import { sidebarMenu } from '../../Navigation/Sidebar'

const page = usePage()
const areaBadges = computed(() => {
    const pendingCount = Number(page.props.pending_appeals ?? 0) + Number(page.props.pending_build_rank_apps ?? 0)
    return { review: pendingCount > 99 ? '99+' : pendingCount }
})
</script>

<template>
    <AdminLayout :area-badges="areaBadges" :navigation="sidebarMenu">
        <template #side-bar>
            <SideBar />
        </template>

        <template #default>
            <slot />
        </template>
    </AdminLayout>
</template>
