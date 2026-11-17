<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import AccountForm from './Partials/AccountForm.vue'
import type { Account } from '../../Data/Account'

function submit(form: InertiaForm<Account>) {
    form.post('/manage/accounts')
}
</script>

<template>
    <ManageFormPage title="Create Account" description="Create a website account that can sign in to Project City Build.">
        <AccountForm :submit="submit" />
    </ManageFormPage>
</template>
