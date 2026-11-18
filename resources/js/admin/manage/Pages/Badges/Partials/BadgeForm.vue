<script setup lang="ts">
import { InertiaForm, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import type { Badge } from '../../../Data/Badge'
import DangerZone from '../../../Components/Forms/DangerZone.vue'
import FormActions from '../../../Components/Forms/FormActions.vue'
import FormErrorSummary from '../../../Components/Forms/FormErrorSummary.vue'
import FormField from '../../../Components/Forms/FormField.vue'
import FormSection from '../../../Components/Forms/FormSection.vue'
import MiniMessageInput from '../../../Components/Forms/MiniMessageInput.vue'
import Input from '../../../../shared/Components/Input.vue'
import { useUnsavedChangesWarning } from '../../../Components/Forms/useUnsavedChangesWarning'

interface Props {
    badge?: Badge,
    submit: (form: InertiaForm<BadgeFormData>) => void,
}

interface BadgeFormData {
    display_name: string,
    unicode_icon: string,
    list_hidden: '1' | '0',
    created_at: Date,
}

const props = defineProps<Props>()

const form = useForm<BadgeFormData>({
    display_name: props.badge?.display_name ?? '',
    unicode_icon: props.badge?.unicode_icon ?? '',
    list_hidden: props.badge?.list_hidden ? '1' : '0',
    created_at: props.badge?.created_at
        ? new Date(props.badge.created_at)
        : new Date(),
})

const isEdit = computed(() => props.badge != null)
useUnsavedChangesWarning(form)

function submit(): void {
    props.submit(form)
}

function destroy(): void {
    if (props.badge) {
        form.delete(`/manage/badges/${props.badge.id}`)
    }
}
</script>

<template>
    <form class="grid gap-6" @submit.prevent="submit">
        <FormErrorSummary :errors="form.errors" />

        <FormSection
            title="Badge Details"
            description="Set the badge name, icon, and visibility."
        >
            <div class="grid gap-4 sm:grid-cols-2">
                <FormField
                    id="display_name"
                    label="Name"
                    :error="form.errors.display_name"
                    required
                    v-slot="field"
                >
                    <Input v-bind="field" v-model="form.display_name" placeholder="eg. Build-Off Winner" />
                </FormField>
                <MiniMessageInput
                    id="unicode_icon"
                    label="Unicode Icon"
                    description="Character/s to use as the badge icon in chat. Supports MiniMessage format."
                    :error="form.errors.unicode_icon"
                    v-model="form.unicode_icon"
                    placeholder="<gold>★</gold>"
                    preview-placeholder="★"
                />
                <p class="text-sm text-muted">
                    See the
                    <a
                        class="text-primary underline"
                        href="https://docs.advntr.dev/minimessage/format.html"
                        target="_blank"
                        rel="noopener noreferrer"
                    >MiniMessage format documentation</a>.
                </p>
                <FormField
                    id="list_hidden"
                    label="Hidden in List"
                    description="Whether this badge will appear in the &quot;attainable badges&quot; list."
                    :error="form.errors.list_hidden"
                    v-slot="field"
                >
                    <input
                        :id="field.id"
                        v-model="form.list_hidden"
                        :aria-invalid="form.errors.list_hidden ? 'true' : 'false'"
                        :aria-describedby="form.errors.list_hidden
                            ? `${field.id}-description ${field.id}-error`
                            : `${field.id}-description`"
                        true-value="1"
                        false-value="0"
                        type="checkbox"
                        class="size-4 rounded border-border-strong accent-primary focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus"
                    >
                </FormField>
            </div>
        </FormSection>

        <FormActions
            :submit-label="isEdit ? 'Update badge' : 'Create badge'"
            cancel-href="/manage/badges"
            :processing="form.processing"
            sticky
        />

        <DangerZone
            v-if="isEdit"
            title="Delete badge"
            description="Permanently remove this badge."
            confirm-message="This cannot be undone."
            confirm-title="Yes, Delete It"
            :disabled="form.processing"
            @confirm="destroy"
        />
    </form>
</template>
