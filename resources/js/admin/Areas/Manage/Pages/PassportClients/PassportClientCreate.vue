<script setup lang="ts">
import ManageFormPage from '../../../../Components/ManageFormPage.vue'
import PassportClientForm from './Partials/PassportClientForm.vue'
import type { PassportClientFormData } from './Partials/PassportClientForm.vue'
import { Routes } from '../../../../Navigation/Routes'
import type { InertiaForm } from '@inertiajs/vue3'

function submit(form: InertiaForm<PassportClientFormData>): void {
    form.post(Routes.manage.passportClients.list)
}
</script>

<template>
    <ManageFormPage
        title="Create Passport Client"
        description="Register an OAuth client for a trusted application or service."
    >
        <PassportClientForm :submit="submit" />
    </ManageFormPage>
</template>
