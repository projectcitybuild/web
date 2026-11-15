<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import Card from '../../shared/Components/Card.vue'
import PageHeader from '../../shared/Components/PageHeader.vue'
import SvgIcon from '../Components/SvgIcon.vue'
import usePermissions from '../Composables/usePermissions'
import { filterSidebar } from '../Navigation/FilterSidebar'
import { sidebarMenu } from '../Navigation/Sidebar'

const { can } = usePermissions()
const sections = computed(() => filterSidebar(sidebarMenu, can))
</script>

<template>
    <Head title="Management" />

    <div class="space-y-6">
        <PageHeader title="Management" />

        <section
            v-if="sections.length > 0"
            aria-label="Management areas"
            class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3"
        >
            <Card v-for="section in sections" :key="section.title">
                <template #header>
                    <div class="flex items-center gap-3">
                        <SvgIcon :svg="section.icon" class="size-5 text-muted" />
                        <h2 class="text-base font-semibold text-foreground">{{ section.title }}</h2>
                    </div>
                </template>

                <ul class="flex flex-col gap-2">
                    <li v-for="item in section.children" :key="item.route">
                        <Link
                            :href="item.route"
                            class="flex min-h-11 items-center rounded-[var(--radius-md)] border border-border px-3 text-sm font-medium text-foreground transition-colors hover:bg-surface-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus focus-visible:ring-offset-2 focus-visible:ring-offset-surface"
                        >
                            {{ item.title }}
                        </Link>
                    </li>
                </ul>
            </Card>
        </section>

        <Card v-else class="text-sm text-muted">
            No management areas are available to your account.
        </Card>
    </div>
</template>
