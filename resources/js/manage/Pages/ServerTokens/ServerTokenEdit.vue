<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import BackButton from '../../Components/BackButton.vue'
import ServerForm from './Partials/ServerTokenForm.vue'
import type { ServerToken } from '../../Data/ServerToken'

interface Props {
    token: ServerToken,
}
const props = defineProps<Props>()

function submit(form) {
    form.put('/manage/server-tokens/' + props.token.id)
}
</script>

<template>
    <section class="p-3 sm:p-5">
        <Head title="Edit a Server Token" />

        <div class="mx-auto max-w-screen-xl">
            <Card>
                <div class="p-8 max-w-2xl">
                    <BackButton href="/manage/server-tokens" class="mb-4" />

                    <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Edit a Server</h2>
                    <div class="text-sm text-gray-500">Registers a server for receiving webhooks and events</div>
                    <hr class="my-6" />

                    <ServerForm :token="props.token" :submit="submit" />
                </div>
            </Card>
        </div>
    </section>
</template>
