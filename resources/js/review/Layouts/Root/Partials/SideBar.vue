<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import AdminNavLink from '../../../../shared/Layouts/Admin/components/AdminNavLink.vue'
import SvgIcon from '../../../../manage/Components/SvgIcon.vue'
import { Icons } from '../../../../manage/Icons'
import usePermissions from '../../../../manage/Composables/usePermissions'
import { sidebarMenu } from '../../../Navigation/Sidebar'

const page = usePage()
const { can } = usePermissions()

const visibleMenu = computed(() => sidebarMenu.filter(item =>
    !item.permission || can(item.permission)
))

function countFor(item: typeof sidebarMenu[number]): number {
    return item.countKey ? Number(page.props[item.countKey] ?? 0) : 0
}
</script>

<template>
    <ul class="space-y-1.5">
        <li v-for="item in visibleMenu" :key="item.route">
            <AdminNavLink :href="item.route" :label="item.title" :badge="countFor(item)">
                <template #icon>
                    <SvgIcon :svg="Icons[item.icon]" :thickness="1.8" class="size-5" />
                </template>
            </AdminNavLink>
        </li>
    </ul>
</template>
