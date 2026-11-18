<script setup lang="ts">
import ManageFormPage from '../../Components/ManageFormPage.vue'
import type { Account } from '../../Data/Account'
import AccountForm from './Partials/AccountForm.vue'

interface Props {
    account: Account,
}
const props = defineProps<Props>()

function submit(form: Parameters<InstanceType<typeof AccountForm>['$props']['submit']>[0]) {
    form.put('/manage/accounts/' + props.account.id)
}
</script>

<template>
    <ManageFormPage title="Edit Account" description="Update this website account and its sign-in details.">
        <AccountForm :account="props.account" :submit="submit" />
    </ManageFormPage>
</template>
