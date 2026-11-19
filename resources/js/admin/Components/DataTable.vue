<script setup lang="ts" generic="T">
import { useSlots } from 'vue'
import Table from './Table.vue'
import TableCell from './TableCell.vue'
import TableHead from './TableHead.vue'
import TableRow from './TableRow.vue'

interface Field {
    key: string,
    label: string,
}

interface Props {
    fields: Field[],
    rows: T[],
    showIndex?: boolean,
    onSelect?: (row: T) => void,
}
withDefaults(defineProps<Props>(), {
    showIndex: false,
})

function getValue(obj: any, path: string) {
    return path.split('.').reduce((acc, part) => acc?.[part], obj)
}

const slots = useSlots()
</script>

<template>
    <Table density="compact" :empty="rows.length === 0" :columns="fields.length + (showIndex ? 1 : 0) + (slots.actions ? 1 : 0)">
        <template #header>
            <TableRow :hoverable="false">
                <TableHead v-if="showIndex">#</TableHead>
                <TableHead v-for="field in fields" :key="field.key">
                    {{ field.label }}
                </TableHead>
                <TableHead v-if="slots.actions" align="end">Actions</TableHead>
            </TableRow>
        </template>

        <TableRow v-for="(row, index) in rows" :key="index" @click="onSelect?.(row)">
            <TableCell v-if="showIndex" class="text-subtle">
                #{{ index + 1 }}
            </TableCell>

            <TableCell v-for="field in fields" :key="field.key" class="whitespace-nowrap">
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
