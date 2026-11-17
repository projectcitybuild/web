<script setup lang="ts">
import AdminNavLink from '../../../../shared/Layouts/Admin/Components/AdminNavLink.vue'
import SvgIcon from '../../../../manage/Components/SvgIcon.vue'
import { Icons } from '../../../../manage/Icons'
import { sidebarMenu } from '../../../Navigation/Sidebar'
</script>

<template>
    <div>
        <ul class="mt-8 space-y-2">
            <li v-for="item in sidebarMenu" :key="item.route">
                <AdminNavLink
                    :href="item.route"
                    :label="item.title"
                    :exact="item.route === '/auditing'"
                >
                    <template #icon>
                        <SvgIcon :svg="Icons[item.icon]" class="size-[18px]" aria-hidden="true" />
                    </template>
                </AdminNavLink>
            </li>
        </ul>
    </div>
</template>
