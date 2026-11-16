<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import { Account } from '../../Data/Account'
import AccountForm from './Partials/AccountForm.vue'

interface Props {
    account: Account,
}
const props = defineProps<Props>()

function submit(form: InertiaForm<Account>) {
    form.put('/manage/accounts/' + props.account.id)
}
</script>

<template>
    <ManageFormPage title="Edit Account" description="Update this website account and its sign-in details." :breadcrumbs="[{ label: 'Manage', href: '/manage' }, { label: 'Accounts', href: '/manage/accounts' }, { label: 'Edit' }]">
        <AccountForm :account="props.account" :submit="submit" />
    </ManageFormPage>
</template>
