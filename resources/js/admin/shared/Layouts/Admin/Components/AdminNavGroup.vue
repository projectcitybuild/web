<script setup lang="ts">
import AdminNavLink from './AdminNavLink.vue'

interface ChildLink {
    title: string
    route: string
    icon: {
        fill?: string
        paths: { d: string; strokeLineCap?: 'butt' | 'round' | 'square'; strokeLineJoin?: 'miter' | 'round' | 'bevel' }[]
    }
}

defineProps<{
    title: string
    children: ChildLink[]
}>()
</script>

<template>
    <li>
        <div class="flex mt-4 mb-2 items-center px-2 text-xs tracking-wide text-muted">
            <span class="min-w-0 flex-1 truncate">{{ title }}</span>
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
