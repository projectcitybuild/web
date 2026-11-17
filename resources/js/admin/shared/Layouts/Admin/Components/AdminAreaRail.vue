<script setup lang="ts">
import logo from '../../../../../../images/logo-alt.png'
import SvgIcon from '../../../../manage/Components/SvgIcon.vue'
import { Icons } from '../../../../manage/Icons'
import { Link } from '@inertiajs/vue3'
import AdminAccountMenu from './AdminAccountMenu.vue'
import useAdminNavigation, { adminAreas } from '../useAdminNavigation'

const props = defineProps<{
    areaBadges: Record<string, number | string>
    collapsed: boolean
}>()

const { currentArea } = useAdminNavigation()
</script>

<template>
    <aside class="fixed inset-y-0 left-0 z-40 hidden w-14 flex-col items-center border-r border-border bg-surface-muted md:flex" aria-label="Area navigation">
        <a href="/" class="flex h-14 w-full items-center justify-center border-b border-border bg-surface transition-colors hover:bg-surface-hover focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-focus motion-reduce:transition-none" aria-label="Project City Build home">
            <img :src="logo" alt="" class="size-7 object-contain" />
        </a>

        <nav class="flex w-full flex-col items-center gap-2.5 py-5" aria-label="Administrative areas">
            <Link
                v-for="area in adminAreas"
                :key="area.id"
                :href="area.href"
                :aria-label="area.label"
                :aria-current="currentArea === area.id ? 'page' : undefined"
                :class="[
                    'group relative flex size-10 items-center justify-center rounded-lg transition-colors duration-150 focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus focus-visible:ring-offset-2 focus-visible:ring-offset-surface-muted motion-reduce:transition-none',
                    currentArea === area.id ? 'bg-surface text-foreground shadow-xs' : 'text-muted hover:bg-surface-hover hover:text-foreground',
                ]"
            >
                <span
                    v-if="currentArea === area.id"
                    class="absolute bottom-2 left-0 h-6 w-0.5 rounded-r bg-primary"
                    aria-hidden="true"
                />
                <SvgIcon :svg="area.id === 'manage' ? Icons.dashboard : Icons.gavel" class="size-5" aria-hidden="true" />

                <span
                    v-if="props.areaBadges[area.id]"
                    class="absolute -right-1 -top-1 flex min-h-4 min-w-4 items-center justify-center rounded-full bg-danger px-1 text-[9px] font-semibold leading-none text-danger-foreground ring-2 ring-surface-muted"
                    :aria-label="`${props.areaBadges[area.id]} pending review items`"
                >
                    {{ props.areaBadges[area.id] }}
                </span>

                <span class="pointer-events-none invisible absolute left-full top-1/2 z-50 ml-3 -translate-y-1/2 whitespace-nowrap rounded-md bg-foreground px-2.5 py-1.5 text-xs font-medium text-background opacity-0 shadow-md transition-opacity group-hover:visible group-hover:opacity-100 group-focus-visible:visible group-focus-visible:opacity-100 motion-reduce:transition-none">
                    {{ area.label }}
                </span>
            </Link>
        </nav>

        <div v-if="props.collapsed" class="mt-auto w-full border-t border-border py-3">
            <AdminAccountMenu collapsed />
        </div>
    </aside>
</template>
