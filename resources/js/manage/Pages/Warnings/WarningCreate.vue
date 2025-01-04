<script setup lang="ts">
import { Head, InertiaForm } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import WarningForm from './Partials/WarningForm.vue'
import BackButton from '../../Components/BackButton.vue'
import { Warning } from 'postcss'

function submit(form: InertiaForm<Warning>) {
    form.post('/manage/warnings')
}
</script>

<template>
    <section>
        <Head title="Create a Player Warning"/>

        <div class="mx-auto max-w-screen-xl">
            <Card>
                <div class="p-8 max-w-2xl">
                    <BackButton href="/manage/warnings" class="mb-4"/>

                    <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Create a Player Warning</h2>
                    <div class="text-sm text-gray-500">Record an infraction against a player</div>
                    <hr class="my-6"/>

                    <WarningForm :submit="submit"/>
                </div>
            </Card>
        </div>
    </section>
</template>
