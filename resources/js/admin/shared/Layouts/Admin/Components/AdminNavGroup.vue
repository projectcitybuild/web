<script setup lang="ts">
import AdminNavLink from './AdminNavLink.vue'
import type { SidebarChild } from '../../../../manage/Navigation/Sidebar'

defineProps<{
    title: string
    children: SidebarChild[]
}>()
</script>

<template>
    <li>
        <div class="flex mt-4 mb-2 items-center px-2 text-xs tracking-wide text-muted/60">
            <span class="min-w-0 flex-1 truncate uppercase">{{ title }}</span>
        </div>
        <ul class="mb-2">
            <li v-for="child in children" :key="child.route">
                <AdminNavLink :href="child.route" :label="child.title">
                    <template #icon>
                        <slot name="child-icon" :child="child" />
                    </template>
                </AdminNavLink>
            </li>
        </ul>
    </li>
</template>
