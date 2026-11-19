<script setup lang="ts">
import { IconChevronDown, IconChevronUp } from '@tabler/icons-vue'
import type { TableSortDirection } from '../Composables/useTableSorting'

type Align = 'start' | 'center' | 'end'

withDefaults(defineProps<{ align?: Align, sortable?: boolean, sortDirection?: TableSortDirection | null }>(), {
    align: 'start',
    sortable: false,
    sortDirection: null,
})

const emit = defineEmits(['sort'])

const alignClasses: Record<Align, string> = {
    start: 'text-left',
    center: 'text-center',
    end: 'text-right',
}
</script>

<template>
    <th
        scope="col"
        class="whitespace-nowrap border-r border-border-table px-3 py-2.5 font-medium last:border-r-0"
        :class="alignClasses[align]"
        :aria-sort="sortDirection === 'asc' ? 'ascending' : sortDirection === 'desc' ? 'descending' : 'none'"
    >
        <button
            v-if="sortable"
            type="button"
            class="inline-flex items-center gap-1 text-inherit hover:text-foreground focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus"
            @click="emit('sort')"
        >
            <slot />
            <IconChevronUp v-if="sortDirection === 'asc'" :size="14" aria-hidden="true" />
            <IconChevronDown v-else-if="sortDirection === 'desc'" :size="14" aria-hidden="true" />
        </button>
        <slot v-else />
    </th>
</template>
