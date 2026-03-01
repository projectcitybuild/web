<script setup lang="ts">
import { Head, InertiaForm } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import BackButton from '../../Components/BackButton.vue'
import ServerForm from './Partials/ServerForm.vue'
import type { Server } from '../../Data/Server'

interface Props {
    server: Server,
}

const props = defineProps<Props>()

function submit(form: InertiaForm<Server>) {
    form.put('/manage/servers/' + props.server.id)
}
</script>

<template>
    <section>
        <Head title="Edit a Server"/>

        <Card>
            <div class="p-8 max-w-2xl">
                <BackButton href="/manage/servers" class="mb-4"/>

                <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Edit a Server</h2>
                <div class="text-sm text-gray-500">Registers a server for receiving webhooks and events</div>
                <hr class="my-6"/>

                <ServerForm :server="props.server" :submit="submit"/>
            </div>
        </Card>
    </section>
</template>
