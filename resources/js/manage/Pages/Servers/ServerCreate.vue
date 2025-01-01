<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import ServerForm from './Partials/ServerForm.vue'
import BackButton from '../../Components/BackButton.vue'
import { InertiaForm } from '@inertiajs/vue3'
import { Server } from '../../Data/Server'

function submit(form: InertiaForm<Server>) {
    form.post('/manage/servers')
}
</script>

<template>
    <section class="p-3 sm:p-5">
        <Head title="Create a Server"/>

        <div class="mx-auto max-w-screen-xl">
            <Card>
                <div class="p-8 max-w-2xl">
                    <BackButton href="/manage/servers" class="mb-4"/>

                    <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Create a Server</h2>
                    <div class="text-sm text-gray-500">Registers a server for receiving webhooks and events</div>
                    <hr class="my-6"/>

                    <ServerForm :submit="submit"/>
                </div>
            </Card>
        </div>
    </section>
</template>
