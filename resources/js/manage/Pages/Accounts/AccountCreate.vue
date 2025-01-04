<script setup lang="ts">
import { Head, InertiaForm } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import AccountForm from './Partials/AccountForm.vue'
import BackButton from '../../Components/BackButton.vue'
import type { Account } from '../../Data/Account'

function submit(form: InertiaForm<Account>) {
    form.post('/manage/accounts')
}
</script>

<template>
    <section class="p-3 sm:p-5">
        <Head title="Create an Account"/>

        <div class="mx-auto max-w-screen-xl">
            <Card>
                <div class="p-8 max-w-2xl">
                    <BackButton href="/manage/accounts" class="mb-4"/>

                    <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Create an Account</h2>
                    <div class="text-sm text-gray-500">Web account that can login</div>
                    <hr class="my-6"/>

                    <AccountForm :submit="submit"/>
                </div>
            </Card>
        </div>
    </section>
</template>
