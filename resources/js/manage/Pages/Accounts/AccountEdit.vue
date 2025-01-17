<script setup lang="ts">
import { Head, InertiaForm } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import BackButton from '../../Components/BackButton.vue'
import { Account } from '../../Data/Account'
import AccountForm from './Partials/AccountForm.vue'

interface Props {
    account: Account,
}
const props = defineProps<Props>()

function submit(form: InertiaForm<Account>) {
    form.put('/manage/accounts/' + props.account.account_id)
}
</script>

<template>
    <section>
        <Head title="Edit an Account"/>

        <Card>
            <div class="p-8 max-w-2xl">
                <BackButton :href="'/manage/accounts/' + account.account_id" class="mb-4"/>

                <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Edit an Account</h2>
                <div class="text-sm text-gray-500">Web account that can login</div>
                <hr class="my-6"/>

                <AccountForm :account="props.account" :submit="submit"/>
            </div>
        </Card>
    </section>
</template>
