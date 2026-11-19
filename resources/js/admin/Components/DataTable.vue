<script setup lang="ts" generic="T">
import { useSlots } from 'vue'
import type { TableSortDirection } from '../Composables/useTableSorting'
import Table from './Table.vue'
import TableCell from './TableCell.vue'
import TableHead from './TableHead.vue'
import TableRow from './TableRow.vue'

interface Field {
    key: string,
    label: string,
    sortable?: boolean,
    sortKey?: string,
}

interface Props {
    fields: Field[],
    rows: T[],
    showIndex?: boolean,
    onSelect?: (row: T) => void,
    sort?: string | null,
    direction?: TableSortDirection | null,
    onSort?: (key: string) => void,
}
const props = withDefaults(defineProps<Props>(), { showIndex: false })

function getValue(obj: any, path: string) {
    return path.split('.').reduce((acc, part) => acc?.[part], obj)
}

const slots = useSlots()
</script>

<template>
    <Table density="compact" :empty="props.rows.length === 0" :columns="props.fields.length + (props.showIndex ? 1 : 0) + (slots.actions ? 1 : 0)">
        <template #header>
            <TableRow :hoverable="false">
                <TableHead v-if="props.showIndex">#</TableHead>
                <TableHead
                    v-for="field in props.fields"
                    :key="field.key"
                    :sortable="props.onSort !== undefined && field.sortable !== false"
                    :sort-direction="props.sort === (field.sortKey ?? field.key) ? props.direction : null"
                    @sort="props.onSort?.(field.sortKey ?? field.key)"
                >
                    {{ field.label }}
                </TableHead>
                <TableHead v-if="slots.actions" align="end">Actions</TableHead>
            </TableRow>
        </template>

        <TableRow v-for="(row, index) in props.rows" :key="index" @click="props.onSelect?.(row)">
            <TableCell v-if="props.showIndex" class="text-subtle">
                #{{ index + 1 }}
            </TableCell>

            <TableCell v-for="field in props.fields" :key="field.key" class="whitespace-nowrap">
                <template v-if="slots[field.key]">
                    <slot :name="field.key" :item="row" />
                </template>
                <span v-else>
                    {{ getValue(row, field.key) }}
                </span>
            </TableCell>

            <TableCell v-if="slots.actions" align="end">
                <slot name="actions" :item="row" />
            </TableCell>
        </TableRow>
    </Table>
</template>
