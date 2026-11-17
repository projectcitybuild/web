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
    <div>
        <div class="border-b border-border px-2.5 pb-7 pt-5">
            <h2 class="text-lg font-semibold leading-7 text-foreground">Review Center</h2>
            <p class="mt-1 text-xs leading-5 text-muted">Moderation and submissions</p>
        </div>
        <p class="mb-3 mt-8 px-2.5 text-xs font-semibold tracking-[0.12em] text-subtle">WORKSPACE</p>
        <ul class="space-y-2">
            <li v-for="item in visibleMenu" :key="item.route">
                <AdminNavLink
                    :href="item.route"
                    :label="item.title"
                    :badge="countFor(item)"
                    :exact="item.route === '/review'"
                >
                    <template #icon>
                        <SvgIcon :svg="Icons[item.icon]" class="size-[18px]" aria-hidden="true" />
                    </template>
                </AdminNavLink>
            </li>
        </ul>

    </div>
</template>
