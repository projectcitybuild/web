<script setup lang="ts">
import { Routes } from '../../../../../Navigation/Routes'
import { InertiaForm, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import DateTimePicker from '../../../../../Components/DateTimePicker.vue'
import DangerZone from '../../../../../Components/Forms/DangerZone.vue'
import FormActions from '../../../../../Components/Forms/FormActions.vue'
import FormErrorSummary from '../../../../../Components/Forms/FormErrorSummary.vue'
import FormField from '../../../../../Components/Forms/FormField.vue'
import FormSection from '../../../../../Components/Forms/FormSection.vue'
import Select from '../../../../../Components/Select.vue'
import Input from '../../../../../Components/Input.vue'
import { useUnsavedChangesWarning } from '../../../../../Components/Forms/useUnsavedChangesWarning'

interface Props {
    perk?: App.Http.Data.AdminDonationPerkData,
    tiers: { id: number, name: string }[],
    submit: (form: InertiaForm<DonationPerkFormData>) => void,
}

interface DonationPerkFormData {
    account_id: number | string,
    donation_id: number | string,
    donation_tier_id: number | string | null,
    is_active: boolean,
    expires_at: Date | null,
}

const props = defineProps<Props>()

const form = useForm<DonationPerkFormData>({
    account_id: props.perk?.account_id ?? '',
    donation_id: props.perk?.donation_id ?? '',
    donation_tier_id: props.perk?.donation_tier_id ?? null,
    is_active: props.perk?.is_active ?? true,
    expires_at: props.perk?.expires_at ? new Date(props.perk.expires_at) : null,
})

const isEdit = computed(() => props.perk != null)
useUnsavedChangesWarning(form)

function submit(): void {
    props.submit(form)
}

function destroy(): void {
    if (props.perk) {
        form.delete(Routes.manage.donationPerks.delete(props.perk.id))
    }
}
</script>

<template>
    <form class="grid gap-6" @submit.prevent="submit">
        <FormErrorSummary :errors="form.errors" />

        <FormSection
            title="Perk Details"
            description="Link this perk to an account and its donation record."
        >
            <div class="grid gap-4 sm:grid-cols-2">
                <FormField
                    id="account_id"
                    label="Account ID"
                    description="The numeric ID of the account receiving this perk."
                    :error="form.errors.account_id"
                    required
                    v-slot="field"
                >
                    <Input v-bind="field" v-model.number="form.account_id" type="number" min="1" />
                </FormField>
                <FormField
                    id="donation_id"
                    label="Donation ID"
                    description="The donation record this perk belongs to."
                    :error="form.errors.donation_id"
                    required
                    v-slot="field"
                >
                    <Input v-bind="field" v-model.number="form.donation_id" type="number" min="1" />
                </FormField>
                <FormField
                    id="donation_tier_id"
                    label="Donation tier"
                    :error="form.errors.donation_tier_id"
                    v-slot="field"
                >
                    <Select v-bind="field" v-model.number="form.donation_tier_id">
                        <option value="">No tier</option>
                        <option v-for="tier in props.tiers" :key="tier.id" :value="tier.id">
                            {{ tier.name }}
                        </option>
                    </Select>
                </FormField>
                <FormField
                    id="expires_at"
                    label="Expires at"
                    :error="form.errors.expires_at"
                    v-slot="field"
                >
                    <DateTimePicker
                        :id="field.id"
                        v-model="form.expires_at"
                        aria-label="Expires at"
                        :aria-invalid="field.ariaInvalid"
                        :aria-describedby="form.errors.expires_at ? field.id + '-error' : undefined"
                    />
                </FormField>
                <FormField
                    id="is_active"
                    label="Active"
                    :error="form.errors.is_active"
                    v-slot="field"
                >
                    <input
                        :id="field.id"
                        v-model="form.is_active"
                        :aria-invalid="field.ariaInvalid"
                        :aria-describedby="form.errors.is_active ? `${field.id}-error` : undefined"
                        type="checkbox"
                        class="size-4 rounded border-border-strong accent-primary focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus"
                    >
                </FormField>
            </div>
        </FormSection>

        <FormActions
            :submit-label="isEdit ? 'Update perk' : 'Create perk'"
            :cancel-href="Routes.manage.donationPerks.list"
            :processing="form.processing"
            sticky
        />

        <DangerZone
            v-if="isEdit"
            title="Delete donation perk"
            description="Permanently remove this account perk."
            confirm-message="This cannot be undone."
            confirm-title="Yes, Delete It"
            :disabled="form.processing"
            @confirm="destroy"
        />
    </form>
</template>
