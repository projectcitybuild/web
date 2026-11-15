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
    <td class="px-4 py-3" :class="alignClasses[align]">
        <slot />
    </td>
</template>
