<script setup lang="ts">
import { Head, InertiaForm } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import ServerTokenForm from './Partials/ServerTokenForm.vue'
import BackButton from '../../Components/BackButton.vue'
import { ServerToken } from '../../Data/ServerToken'

function submit(form: InertiaForm<ServerToken>) {
    form.post('/manage/server-tokens')
}
</script>

<template>
    <section class="p-3 sm:p-5">
        <Head title="Create a Server Token"/>

        <div class="mx-auto max-w-screen-xl">
            <Card>
                <div class="p-8 max-w-2xl">
                    <BackButton href="/manage/server-tokens" class="mb-4"/>

                    <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Create a Server</h2>
                    <div class="text-sm text-gray-500">Creates an authorization token that grants access to the PCB API.
                        For use with first-party applications only
                    </div>
                    <hr class="my-6"/>

                    <ServerTokenForm :submit="submit"/>
                </div>
            </Card>
        </div>
    </section>
</template>
