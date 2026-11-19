<script setup lang="ts">
import { ref } from 'vue'
import Button from '../Button.vue'
import ConfirmDialog from '../ConfirmDialog.vue'

interface Props {
    title?: string,
    description: string,
    confirmMessage: string,
    confirmTitle?: string,
    disabled?: boolean,
}

withDefaults(defineProps<Props>(), {
    title: 'Danger zone',
    confirmTitle: 'Delete',
    disabled: false,
})

const emit = defineEmits<{
    confirm: [],
}>()

const confirmDialog = ref<InstanceType<typeof ConfirmDialog>>()

function confirm(): void {
    confirmDialog.value?.close()
    emit('confirm')
}
</script>

<template>
    <section class="grid gap-4 rounded-lg border border-danger/50 bg-danger-soft/30 p-5 sm:p-6">
        <header class="grid gap-1">
            <h2 class="text-base font-semibold text-danger">{{ title }}</h2>
            <p class="text-sm text-muted">{{ description }}</p>
        </header>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-muted">
                This action cannot be undone.
            </p>
            <Button
                type="button"
                variant="danger"
                :disabled="disabled"
                @click="confirmDialog?.open()"
            >
                <slot name="button">Delete</slot>
            </Button>
        </div>
        <ConfirmDialog
            ref="confirmDialog"
            :message="confirmMessage"
            :confirm-title="confirmTitle"
            :on-confirm="confirm"
        />
    </section>
</template>
