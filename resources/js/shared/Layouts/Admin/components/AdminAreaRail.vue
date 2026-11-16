<script setup lang="ts">
import logo from '../../../../../images/logo-alt.png'
import SvgIcon from '../../../../manage/Components/SvgIcon.vue'
import { Icons } from '../../../../manage/Icons'
import useAdminNavigation, { adminAreas } from '../useAdminNavigation'

const props = defineProps<{
    areaBadges: Record<string, number | string>
}>()

const { currentArea } = useAdminNavigation()
</script>

<template>
    <aside class="fixed inset-y-0 left-0 z-30 hidden w-11 flex-col items-center border-r border-border bg-surface-muted md:flex" aria-label="Area navigation">
        <a href="/" class="flex h-14 w-full items-center justify-center border-b border-border" aria-label="Project City Build home">
            <img :src="logo" alt="" class="size-7 object-contain" />
        </a>

        <nav class="flex w-full flex-col items-center gap-3 py-5" aria-label="Administrative areas">
            <a
                v-for="area in adminAreas"
                :key="area.id"
                :href="area.href"
                :aria-label="area.label"
                :aria-current="currentArea === area.id ? 'page' : undefined"
                :class="[
                    'group relative flex size-9 items-center justify-center rounded-md transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus focus-visible:ring-offset-2 focus-visible:ring-offset-surface-muted motion-reduce:transition-none',
                    currentArea === area.id ? 'bg-primary text-primary-foreground' : 'text-muted hover:bg-surface-hover hover:text-foreground',
                ]"
            >
                <SvgIcon :svg="area.id === 'manage' ? Icons.dashboard : Icons.gavel" class="size-4" aria-hidden="true" />

                <span
                    v-if="props.areaBadges[area.id]"
                    class="absolute -right-1 -top-1 flex min-h-4 min-w-4 items-center justify-center rounded-full bg-danger px-1 text-[9px] font-semibold leading-none text-danger-foreground ring-2 ring-surface-muted"
                    :aria-label="`${props.areaBadges[area.id]} pending review items`"
                >
                    {{ props.areaBadges[area.id] }}
                </span>

                <span class="pointer-events-none invisible absolute left-full top-1/2 z-50 ml-3 -translate-y-1/2 whitespace-nowrap rounded bg-[#1f2937] px-2.5 py-1.5 text-xs font-medium text-white opacity-0 shadow-md transition-opacity group-hover:visible group-hover:opacity-100 group-focus-visible:visible group-focus-visible:opacity-100 motion-reduce:transition-none">
                    {{ area.label }}
                </span>
            </a>
        </nav>
    </aside>
</template>
