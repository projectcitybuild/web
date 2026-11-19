<script setup lang="ts">
import { Routes } from '../../../../../Navigation/Routes'
import { InertiaForm, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import DateTimeInput from '../../../../../Components/Forms/DateTimeInput.vue'
import FormActions from '../../../../../Components/Forms/FormActions.vue'
import FormErrorSummary from '../../../../../Components/Forms/FormErrorSummary.vue'
import FormField from '../../../../../Components/Forms/FormField.vue'
import FormNotice from '../../../../../Components/Forms/FormNotice.vue'
import FormSection from '../../../../../Components/Forms/FormSection.vue'
import { useUnsavedChangesWarning } from '../../../../../Components/Forms/useUnsavedChangesWarning'
import Input from '../../../../../Components/Input.vue'

interface Props {
    account?: App.Http.Data.AdminAccountData,
    submit: (form: InertiaForm<AccountFormData>) => void,
}

interface AccountFormData {
    username: string,
    email: string,
    password: string | null,
    created_at: Date,
}

const props = defineProps<Props>()

const form = useForm<AccountFormData>({
    username: props.account?.username ?? '',
    email: props.account?.email ?? '',
    password: null,
    created_at: props.account?.created_at
        ? new Date(props.account.created_at)
        : new Date(),
})

const isEdit = computed(() => props.account != null)
useUnsavedChangesWarning(form)

function submit(): void {
    if (!form.processing) {
        props.submit(form)
    }
}
</script>

<template>
    <form class="grid gap-6" @submit.prevent="submit">
        <FormErrorSummary :errors="form.errors" />

        <FormSection
            title="Account Information"
            description="Set the account's sign-in name and email address."
        >
            <div class="grid gap-4 sm:grid-cols-2">
                <FormField
                    id="username"
                    label="Username"
                    :error="form.errors.username"
                    required
                    v-slot="field"
                >
                    <Input v-bind="field" v-model="form.username" autocomplete="username" />
                </FormField>
                <FormField
                    id="email"
                    label="Email address"
                    :error="form.errors.email"
                    required
                    v-slot="field"
                >
                    <Input v-bind="field" v-model="form.email" type="email" autocomplete="email" />
                </FormField>
            </div>
        </FormSection>

        <FormSection
            title="Authentication"
            description="Manage the password used to sign in to the website."
        >
            <FormField
                id="password"
                :label="isEdit ? 'New password' : 'Password'"
                :description="isEdit
                    ? 'Leave blank to keep the current password.'
                    : 'Use a strong password that meets the configured password policy.'"
                :error="form.errors.password"
                :required="!isEdit"
                v-slot="field"
            >
                <Input
                    v-bind="field"
                    v-model="form.password"
                    type="password"
                    autocomplete="new-password"
                />
            </FormField>
        </FormSection>

        <FormSection
            title="Account Metadata"
            description="Review the account creation date."
        >
            <DateTimeInput
                id="created_at"
                label="Created at"
                :error="form.errors.created_at"
                required
                v-model="form.created_at"
            />
        </FormSection>

        <FormNotice v-if="!isEdit" type="warning" title="Account activation">
            This creates an unactivated account. After creation, either force-activate the account or send its activation email.
        </FormNotice>

        <FormActions
            :submit-label="isEdit ? 'Update account' : 'Create account'"
            :cancel-href="Routes.manage.accounts.list"
            :processing="form.processing"
            sticky
        />
    </form>
</template>
