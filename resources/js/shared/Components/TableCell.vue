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
    <td class="border-r border-border-table px-3 py-2 last:border-r-0" :class="alignClasses[align]">
        <slot />
    </td>
</template>
