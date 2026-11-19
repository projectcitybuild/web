<script setup lang="ts">
import { Routes } from '../../../../../Navigation/Routes'
import { InertiaForm, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import DateTimePicker from '../../../../../Components/DateTimePicker.vue'
import PlayerPicker from '../../../../../Components/PlayerPicker.vue'
import DangerZone from '../../../../../Components/Forms/DangerZone.vue'
import FormActions from '../../../../../Components/Forms/FormActions.vue'
import FormErrorSummary from '../../../../../Components/Forms/FormErrorSummary.vue'
import FormField from '../../../../../Components/Forms/FormField.vue'
import FormSection from '../../../../../Components/Forms/FormSection.vue'
import Textarea from '../../../../../Components/Textarea.vue'
import { useUnsavedChangesWarning } from '../../../../../Components/Forms/useUnsavedChangesWarning'

interface Props {
    warning?: App.Http.Data.AdminPlayerWarningData,
    submit: (form: InertiaForm<PlayerWarningFormData>) => void,
}

interface PlayerWarningFormData {
    warned_uuid: string | null,
    warned_alias: string | null,
    warner_uuid: string | null,
    warner_alias: string | null,
    reason: string,
    additional_info: string,
    created_at: Date,
}

const props = defineProps<Props>()

const uuidParam = new URLSearchParams(window.location.search).get('uuid') ?? undefined

const form = useForm<PlayerWarningFormData>({
    warned_uuid: props.warning?.warned_player.uuid ?? null,
    warned_alias: props.warning?.warned_player.alias ?? null,
    warner_uuid: props.warning?.warner_player.uuid ?? null,
    warner_alias: props.warning?.warner_player.alias ?? null,
    reason: props.warning?.reason ?? '',
    additional_info: props.warning?.additional_info ?? '',
    created_at: props.warning?.created_at
        ? new Date(props.warning.created_at)
        : new Date(),
})

const isEdit = computed(() => props.warning != null)
useUnsavedChangesWarning(form)

function submit(): void {
    props.submit(form)
}

function destroy(): void {
    if (props.warning) {
        form.delete(`/manage/warnings/${props.warning.id}`)
    }
}
</script>

<template>
    <form class="grid gap-6" @submit.prevent="submit">
        <FormErrorSummary :errors="form.errors" />

        <FormSection
            title="Warning Details"
            description="Select the player and record the warning date, reason, and staff information."
        >
            <div class="grid gap-4 sm:grid-cols-2">
                <FormField
                    id="warned_uuid"
                    label="Player"
                    :error="form.errors.warned_uuid ?? undefined"
                    required
                    class="sm:col-span-2"
                >
                    <PlayerPicker
                        id="warned_uuid"
                        v-model:uuid="form.warned_uuid"
                        v-model:alias="form.warned_alias"
                        :initial-search="uuidParam"
                    />
                </FormField>
                <FormField
                    id="created_at"
                    label="Date of Warning"
                    :error="form.errors.created_at ?? undefined"
                    required
                    v-slot="field"
                >
                    <DateTimePicker
                        :id="field.id"
                        v-model="form.created_at"
                        aria-label="Date of Warning"
                        :aria-invalid="field.ariaInvalid"
                        :aria-describedby="form.errors.created_at ? field.id + '-error' : undefined"
                        @change="form.created_at = $event"
                    />
                </FormField>
                <FormField
                    id="warner_uuid"
                    label="Warned By"
                    :error="form.errors.warner_uuid ?? undefined"
                >
                    <PlayerPicker
                        id="warner_uuid"
                        v-model:uuid="form.warner_uuid"
                        v-model:alias="form.warner_alias"
                    />
                </FormField>
                <FormField
                    id="reason"
                    label="Reason"
                    :error="form.errors.reason ?? undefined"
                    required
                    class="sm:col-span-2"
                    v-slot="field"
                >
                    <Textarea v-bind="field" v-model="form.reason" rows="3" />
                </FormField>
                <FormField
                    id="additional_info"
                    label="Additional Information"
                    :error="form.errors.additional_info ?? undefined"
                    class="sm:col-span-2"
                    v-slot="field"
                >
                    <Textarea v-bind="field" v-model="form.additional_info" rows="8" />
                </FormField>
            </div>
        </FormSection>

        <FormActions
            :submit-label="isEdit ? 'Update warning' : 'Create warning'"
            :cancel-href="Routes.manage.warnings.list"
            :processing="form.processing"
            sticky
        />

        <DangerZone
            v-if="isEdit"
            title="Delete warning"
            description="Permanently remove this warning."
            confirm-message="This cannot be undone."
            confirm-title="Yes, Delete It"
            :disabled="form.processing"
            @confirm="destroy"
        />
    </form>
</template>
