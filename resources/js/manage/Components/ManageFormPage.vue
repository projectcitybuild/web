<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import Card from './Card.vue'
import PageHeader from '../../shared/Components/PageHeader.vue'

interface Props {
    title: string,
    description?: string,
    framed?: boolean,
    wide?: boolean,
}

withDefaults(defineProps<Props>(), {
    framed: true,
    wide: false,
})
defineSlots<{
    default?: () => unknown,
    actions?: () => unknown,
}>()
</script>

<template>
    <div class="manage-form-page mx-auto w-full space-y-6" :class="wide ? 'max-w-none' : 'max-w-5xl'">
        <Head :title="title" />

        <PageHeader :title="title" :description="description">
            <template v-if="$slots.actions" #actions>
                <slot name="actions" />
            </template>
        </PageHeader>

        <template v-if="framed">
            <Card padding="none" :class="wide ? 'max-w-none' : 'max-w-4xl'">
                <div class="space-y-6 p-5 sm:p-8">
                    <slot />
                </div>
            </Card>
        </template>
        <slot v-else />
    </div>
</template>
