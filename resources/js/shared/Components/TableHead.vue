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
    <th scope="col" class="whitespace-nowrap bg-surface-muted px-4 py-3 font-medium" :class="alignClasses[align]">
        <slot />
    </th>
</template>
