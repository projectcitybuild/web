<script setup lang="ts">
type Align = 'start' | 'center' | 'end'

withDefaults(defineProps<{ align?: Align }>(), {
    align: 'start',
})

const alignClasses: Record<Align, string> = {
    start: 'text-left',
    center: 'text-center',
    end: 'text-right',
}
</script>

<template>
    <th scope="col" class="whitespace-nowrap border-r border-border-table px-3 py-2.5 font-medium last:border-r-0" :class="alignClasses[align]">
        <slot />
    </th>
</template>
