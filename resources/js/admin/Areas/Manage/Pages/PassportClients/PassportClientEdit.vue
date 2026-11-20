<script setup lang="ts">
import ManageFormPage from '../../../../Components/ManageFormPage.vue'
import PassportClientForm from './Partials/PassportClientForm.vue'
import type { PassportClientFormData } from './Partials/PassportClientForm.vue'
import { Routes } from '../../../../Navigation/Routes'
import type { InertiaForm } from '@inertiajs/vue3'
import SuccessAlert from '../../../../Components/SuccessAlert.vue'
import CopyIconButton from '../../../../Components/CopyIconButton.vue'

interface PassportClient {
    id: number,
    name: string,
    grant_types: string[],
    redirect_uris: string[],
    confidential: boolean,
    revoked: boolean,
}

const props = defineProps<AdminSharedPageProps & {
    client: PassportClient,
    regenerated_client_secret?: string | null,
}>()

function submit(form: InertiaForm<PassportClientFormData>): void {
    form.put(Routes.manage.passportClients.update(props.client.id))
}
</script>

<template>
    <ManageFormPage
        title="Edit Passport Client"
        description="Update this OAuth client's name and redirect URIs."
    >
        <SuccessAlert v-if="success" :message="success" />
        <section
            v-if="regenerated_client_secret"
            class="rounded-lg border border-danger bg-surface p-5 sm:p-6"
            aria-labelledby="regenerated-client-secret-title"
        >
            <h2 id="regenerated-client-secret-title" class="font-semibold text-foreground">
                Copy the new client secret now
            </h2>
            <p class="mt-1 text-sm text-muted">
                The previous secret is no longer valid. Passport will not show the new secret again.
            </p>
            <div class="mt-3 flex items-start gap-2">
                <code class="min-w-0 flex-1 select-all break-all rounded-md bg-surface-muted p-3 font-mono text-sm text-foreground">
                    {{ regenerated_client_secret }}
                </code>
                <CopyIconButton :text="regenerated_client_secret ?? null" label="client secret" />
            </div>
        </section>
        <PassportClientForm :client="props.client" :submit="submit" />
    </ManageFormPage>
</template>
