<script setup lang="ts">
import AdminNavGroup from '../../../../shared/Layouts/Admin/components/AdminNavGroup.vue'
import AdminNavLink from '../../../../shared/Layouts/Admin/components/AdminNavLink.vue'
import SvgIcon from '../../../Components/SvgIcon.vue'
import usePermissions from '../../../Composables/usePermissions';
import { filterSidebar } from '../../../Navigation/FilterSidebar';
import { sidebarMenu } from '../../../Navigation/Sidebar';
import { computed } from 'vue';

const { can } = usePermissions()

const filteredMenu = computed(() =>
  filterSidebar(sidebarMenu, can)
)
</script>

<template>
    <ul class="space-y-1.5">
        <li>
            <AdminNavLink href="/manage" label="Dashboard">
                <template #icon>
                    <svg class="size-5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75h6.5v6.5h-6.5zm10 0h6.5v6.5h-6.5zm-10 10h6.5v6.5h-6.5zm10 0h6.5v6.5h-6.5z" />
                    </svg>
                </template>
            </AdminNavLink>
        </li>
        <AdminNavGroup
            v-for="section in filteredMenu"
            :key="section.title"
            :title="section.title"
            :children="section.children"
        >
            <template #child-icon="{ child }">
                <SvgIcon :svg="child.icon" :thickness="2" class="size-4" />
            </template>
        </AdminNavGroup>
        <li class="mt-4 border-t border-border pt-4">
            <AdminNavLink href="/manage/activity" label="Audit Logs">
                <template #icon>
                    <svg class="size-5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16v16H4zm0 4h16M8 4v16" />
                    </svg>
                </template>
            </AdminNavLink>
        </li>
    </ul>
</template>
