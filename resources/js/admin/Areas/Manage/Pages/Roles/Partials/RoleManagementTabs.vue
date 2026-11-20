<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Routes } from '../../../../../Navigation/Routes'

interface Props {
    activeTab: 'roles' | 'discord-role-mappings',
}

defineProps<Props>()
</script>

<template>
    <nav aria-label="Role management pages" class="flex gap-6 overflow-x-auto border-b border-border">
        <Link
            :href="Routes.manage.roles.list"
            class="shrink-0 border-b-2 px-1 pb-3 text-sm font-medium transition-colors"
            :class="activeTab === 'roles' ? 'border-primary text-primary' : 'border-transparent text-muted hover:text-foreground dark:text-subtle dark:hover:text-foreground'"
            :aria-current="activeTab === 'roles' ? 'page' : undefined"
        >
            Roles
        </Link>
        <Link
            :href="Routes.manage.discordRoleMappings.list"
            class="shrink-0 border-b-2 px-1 pb-3 text-sm font-medium transition-colors"
            :class="activeTab === 'discord-role-mappings' ? 'border-primary text-primary' : 'border-transparent text-muted hover:text-foreground dark:text-subtle dark:hover:text-foreground'"
            :aria-current="activeTab === 'discord-role-mappings' ? 'page' : undefined"
        >
            Discord Role Mappings
        </Link>
    </nav>
</template>
