<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import Card from './ManageCard.vue'
import PageHeader from './PageHeader.vue'

interface Props {
    title: string,
    description?: string,
    framed?: boolean,
    wide?: boolean,
    layout?: 'standard' | 'wide' | 'split',
}

withDefaults(defineProps<Props>(), {
    framed: true,
    wide: false,
    layout: 'standard',
})
defineSlots<{
    default?: () => unknown,
    actions?: () => unknown,
    preview?: () => unknown,
}>()
</script>

<template>
    <div
        class="manage-form-page mx-auto w-full space-y-6"
        :class="wide || layout === 'wide' ? 'max-w-none' : 'max-w-6xl'"
    >
        <Head :title="title" />

        <PageHeader :title="title" :description="description">
            <template v-if="$slots.actions" #actions>
                <slot name="actions" />
            </template>
        </PageHeader>

        <div
            class="grid items-start gap-6 xl:gap-8"
            :class="[
                layout === 'split' || $slots.preview
                    ? 'grid-cols-1 xl:grid-cols-[minmax(0,1.5fr)_minmax(18rem,0.8fr)]'
                    : '',
            ]"
        >
            <Card v-if="framed" padding="none" :class="wide || layout === 'wide' ? 'max-w-none' : 'max-w-5xl'">
                <div class="space-y-6 p-5 sm:p-8">
                    <slot />
                </div>
            </Card>
            <div v-else class="min-w-0">
                <slot />
            </div>
            <aside v-if="$slots.preview" class="min-w-0">
                <slot name="preview" />
            </aside>
        </div>
    </div>
</template>
