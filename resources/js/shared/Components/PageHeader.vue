<script setup lang="ts">
import { inject, onBeforeUnmount, watch } from 'vue'
import type { Ref } from 'vue'

interface Props {
    title: string,
    description?: string,
}

const props = defineProps<Props>()

const pageTitle = inject<Ref<string>>('adminPageTitle')

watch(() => props.title, title => {
    if (pageTitle) {
        pageTitle.value = title
    }
}, { immediate: true })

onBeforeUnmount(() => {
    if (pageTitle) {
        pageTitle.value = ''
    }
})
</script>

<template>
    <header class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <nav v-if="$slots.breadcrumbs" aria-label="Breadcrumb" class="mb-2 text-sm text-muted">
                <slot name="breadcrumbs" />
            </nav>
            <h1 class="text-2xl font-semibold leading-tight text-foreground">
                {{ title }}
            </h1>
            <p v-if="description" class="mt-1 text-sm text-muted">
                {{ description }}
            </p>
        </div>
        <div v-if="$slots.actions" class="flex w-full flex-wrap gap-2 sm:w-auto sm:justify-end">
            <slot name="actions" />
        </div>
    </header>
</template>
