<script setup lang="ts">
import AdminNavGroup from '../../../../shared/Layouts/Admin/components/AdminNavGroup.vue'
import AdminNavLink from '../../../../shared/Layouts/Admin/components/AdminNavLink.vue'
import SvgIcon from '../../../Components/SvgIcon.vue'
import { Icons } from '../../../Icons'
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
    <ul class="space-y-1.5 mt-4">
        <li>
            <AdminNavLink href="/manage" label="Dashboard">
                <template #icon>
                    <SvgIcon :svg="Icons.dashboard" class="size-5" aria-hidden="true" />
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
                <SvgIcon :svg="child.icon" class="size-4" />
            </template>
        </AdminNavGroup>
        <li class="mt-4 border-t border-border pt-4">
            <AdminNavLink href="/manage/activity" label="Audit Logs">
                <template #icon>
                    <SvgIcon :svg="Icons.auditLogs" class="size-5" aria-hidden="true" />
                </template>
            </AdminNavLink>
        </li>
    </ul>
</template>
