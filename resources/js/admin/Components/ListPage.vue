<script setup lang="ts">
import { computed, ref, useId } from 'vue'
import Card from './ManageCard.vue'
import Button from './Button.vue'
import SvgIcon from './SvgIcon.vue'
import { Icons } from '../Components/SvgIcon.vue'

interface Props {
    count?: number | null,
    from?: number | null,
    to?: number | null,
    total?: number | null,
    countLabel?: string,
    filterCount?: number,
}

const props = withDefaults(defineProps<Props>(), {
    filterCount: 0,
})

defineSlots<{
    controls?: () => unknown,
    filters?: () => unknown,
    default?: () => unknown,
    footer?: () => unknown,
}>()

const filtersExpanded = ref(false)
const filterRegionId = `manage-list-filters-${useId()}`

const countText = computed(() => {
    if (props.count == null && props.from == null && props.to == null) {
        return null
    }

    const label = props.countLabel ?? ''
    const from = props.from ?? (props.count && props.count > 0 ? 1 : 0)
    const to = props.to ?? props.count ?? 0
    const total = props.total ?? props.count

    if (total != null) {
        return `Showing ${from}-${to} of ${total} ${label}`.trim()
    }

    return `Showing ${from}-${to} ${label}`.trim()
})
</script>

<template>
    <Card
        padding="none"
        class="overflow-hidden rounded-[var(--radius-md)] border border-border bg-surface [&_.table-frame]:border-0 [&_.pagination-end]:border-0 [&_.pagination-spinner]:border-0"
    >
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-border bg-surface p-4">
            <p v-if="countText" class="text-sm text-muted" aria-live="polite">
                {{ countText }}
            </p>

            <div v-if="$slots.controls || $slots.filters" class="ml-auto flex flex-wrap items-center justify-end gap-2">
                <slot name="controls" />
                <Button
                    v-if="$slots.filters"
                    variant="outline"
                    :aria-expanded="filtersExpanded"
                    :aria-controls="filterRegionId"
                    :aria-label="filtersExpanded ? 'Hide filters' : 'Show filters'"
                    @click="filtersExpanded = !filtersExpanded"
                >
                    <SvgIcon :svg="Icons.filter" aria-hidden="true" />
                    <span>Filters</span>
                    <span
                        v-if="filterCount > 0"
                        class="inline-flex min-w-5 items-center justify-center rounded-full bg-primary px-1.5 py-0.5 text-xs font-semibold text-primary-foreground"
                        aria-hidden="true"
                    >
                        {{ filterCount }}
                    </span>
                    <span v-if="filterCount > 0" class="sr-only">{{ filterCount }} active</span>
                </Button>
            </div>
        </div>

        <div
            v-if="$slots.filters"
            v-show="filtersExpanded"
            :id="filterRegionId"
            class="border-b border-border bg-surface-muted p-4"
        >
            <slot name="filters" />
        </div>

        <slot />

        <div v-if="$slots.footer" class="border-t border-border p-4">
            <slot name="footer" />
        </div>
    </Card>
</template>
