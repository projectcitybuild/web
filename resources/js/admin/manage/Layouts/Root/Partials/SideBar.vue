<script setup lang="ts">
import AdminNavGroup from '../../../../shared/Layouts/Admin/Components/AdminNavGroup.vue'
import AdminNavLink from '../../../../shared/Layouts/Admin/Components/AdminNavLink.vue'
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
    <ul class="mt-4 space-y-1.5">
        <li>
            <AdminNavLink href="/manage" label="Overview" exact>
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
    </ul>
</template>
