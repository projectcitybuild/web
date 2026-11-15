<script setup lang="ts">
import { useAttrs } from 'vue'

type Density = 'default' | 'compact'

interface Props {
    density?: Density,
    empty?: boolean,
    emptyMessage?: string,
    columns?: number,
}

defineOptions({ inheritAttrs: false })

withDefaults(defineProps<Props>(), {
    density: 'default',
    empty: false,
    emptyMessage: 'No results found.',
    columns: 1,
})

const attrs = useAttrs()
</script>

<template>
    <div class="w-full overflow-x-auto">
        <table
            v-bind="attrs"
            class="w-full border-collapse text-left text-foreground"
            :class="density === 'compact' ? 'text-xs' : 'text-sm'"
        >
            <thead v-if="$slots.header" class="bg-surface-muted text-xs text-subtle">
                <slot name="header" />
            </thead>
            <tbody class="divide-y divide-border">
                <tr v-if="empty">
                    <td :colspan="columns" class="px-4 py-8 text-center text-sm text-muted">
                        <slot name="empty">{{ emptyMessage }}</slot>
                    </td>
                </tr>
                <slot v-else />
            </tbody>
        </table>
    </div>
</template>
