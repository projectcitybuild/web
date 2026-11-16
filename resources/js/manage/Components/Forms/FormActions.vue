<script setup lang="ts">
import Button from '../../../shared/Components/Button.vue'
import { Link } from '@inertiajs/vue3'

interface Props {
    submitLabel?: string,
    cancelLabel?: string,
    cancelHref?: string,
    processing?: boolean,
    disabled?: boolean,
    sticky?: boolean,
}

withDefaults(defineProps<Props>(), {
    submitLabel: 'Save changes',
    cancelLabel: 'Cancel',
    processing: false,
    disabled: false,
    sticky: false,
})

defineEmits<{
    cancel: [],
}>()
</script>

<template>
    <div
        class="flex flex-col-reverse gap-3 border-t border-border bg-surface pt-4 sm:flex-row sm:items-center sm:justify-between"
        :class="sticky ? 'sticky bottom-0 z-10 -mx-5 px-5 pb-[calc(1rem+env(safe-area-inset-bottom))] sm:-mx-8 sm:px-8' : ''"
    >
        <div class="flex flex-col-reverse gap-3 sm:flex-row">
            <Link
                v-if="cancelHref"
                :href="cancelHref"
                class="inline-flex min-h-11 items-center justify-center rounded-md px-4 text-sm font-medium text-muted hover:bg-surface-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus"
            >
                {{ cancelLabel }}
            </Link>
            <Button v-else variant="outline" type="button" class="min-h-11" @click="$emit('cancel')">
                {{ cancelLabel }}
            </Button>
            <slot name="destructive" />
        </div>
        <Button
            type="submit"
            variant="primary"
            class="min-h-11 w-full sm:w-auto"
            :loading="processing"
            :disabled="disabled"
        >
            {{ submitLabel }}
        </Button>
    </div>
</template>
