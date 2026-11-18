<script setup lang="ts">
import ManageFormPage from '../../Components/ManageFormPage.vue'
import AccountForm from './Partials/AccountForm.vue'

defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/Accounts/AccountCreate']>()

function submit(form: Parameters<InstanceType<typeof AccountForm>['$props']['submit']>[0]) {
    form.post('/manage/accounts')
}
</script>

<template>
    <ManageFormPage title="Create Account" description="Create a website account that can sign in to Project City Build.">
        <AccountForm :submit="submit" />
    </ManageFormPage>
</template>
