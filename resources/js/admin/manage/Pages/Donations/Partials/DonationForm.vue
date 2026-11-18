<script setup lang="ts">
import { InertiaForm, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import DateTimePicker from '../../../Components/DateTimePicker.vue'
import DangerZone from '../../../Components/Forms/DangerZone.vue'
import FormActions from '../../../Components/Forms/FormActions.vue'
import FormErrorSummary from '../../../Components/Forms/FormErrorSummary.vue'
import FormField from '../../../Components/Forms/FormField.vue'
import FormSection from '../../../Components/Forms/FormSection.vue'
import { useUnsavedChangesWarning } from '../../../Components/Forms/useUnsavedChangesWarning'
import Input from '../../../../shared/Components/Input.vue'

interface Props {
    donation?: App.Http.Data.AdminDonationData,
    submit: (form: InertiaForm<DonationFormData>) => void,
}

interface DonationFormData {
    amount: number | string,
    created_at: Date,
}

const props = defineProps<Props>()

const form = useForm<DonationFormData>({
    amount: props.donation?.amount ?? 1,
    created_at: props.donation?.created_at
        ? new Date(props.donation.created_at)
        : new Date(),
})

const isEdit = computed(() => props.donation != null)
useUnsavedChangesWarning(form)

function submit(): void {
    props.submit(form)
}

function destroy(): void {
    if (props.donation) {
        form.delete(`/manage/donations/${props.donation.id}`)
    }
}
</script>

<template>
    <form class="grid gap-6" @submit.prevent="submit">
        <FormErrorSummary :errors="form.errors" />

        <FormSection
            title="Donation Details"
            description="Record the donation amount and date."
        >
            <div class="grid gap-4 sm:grid-cols-2">
                <FormField
                    id="amount"
                    label="Amount in dollars (AUD)"
                    :error="form.errors.amount"
                    required
                    v-slot="field"
                >
                    <Input v-bind="field" v-model="form.amount" type="number" />
                </FormField>
                <FormField
                    id="created_at"
                    label="Date"
                    :error="form.errors.created_at"
                    required
                    v-slot="field"
                >
                    <DateTimePicker
                        :id="field.id"
                        v-model="form.created_at"
                        aria-label="Date"
                        :aria-invalid="field.ariaInvalid"
                        :aria-describedby="form.errors.created_at ? field.id + '-error' : undefined"
                        @change="form.created_at = $event"
                    />
                </FormField>
            </div>
        </FormSection>

        <FormActions
            :submit-label="isEdit ? 'Update donation' : 'Create donation'"
            cancel-href="/manage/donations"
            :processing="form.processing"
            sticky
        />

        <DangerZone
            v-if="isEdit"
            title="Delete donation"
            description="Permanently remove this donation record."
            confirm-message="This cannot be undone."
            confirm-title="Yes, Delete It"
            :disabled="form.processing"
            @confirm="destroy"
        />
    </form>
</template>
