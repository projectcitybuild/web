<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import BanForm from "./Partials/BanForm.vue";

const props = defineProps({
    ban: Object,
})

function submit(form) {
    form.put('/manage/player-bans/' + props.ban.id, {
        preserveScroll: true,
    })
}
</script>

<template>
    <section class="p-3 sm:p-5">
        <Head title="Create a Ban" />

        <div class="mx-auto max-w-screen-xl pb-8 px-8">
            <h1 class="text-3xl text-gray-600">
                Manage Bans
            </h1>
        </div>

        <div class="mx-auto max-w-screen-xl">
            <Card>
                <div class="p-8 max-w-2xl">
                    <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Edit a Ban</h2>
                    <div class="text-sm text-gray-500">Prevent a Minecraft UUID from connecting to our server</div>
                    <hr class="my-6" />

                    <BanForm :ban="props.ban" :submit="submit" />
                </div>
            </Card>

        </div>
    </section>
</template>
