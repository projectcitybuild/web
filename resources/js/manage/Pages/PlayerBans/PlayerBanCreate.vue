<script setup>
import { Head } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import BanForm from './Partials/PlayerBanForm.vue'
import BackButton from '../../Components/BackButton.vue'

function submit(form) {
    form.post('/manage/player-bans', {
        preserveScroll: true,
    })
}
</script>

<template>
    <section class="p-3 sm:p-5">
        <Head title="Create a Ban" />

        <div class="mx-auto max-w-screen-xl">
            <Card>
                <div class="p-8 max-w-2xl">
                    <BackButton href="/manage/player-bans" class="mb-4" />

                    <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Create a Ban</h2>
                    <div class="text-sm text-gray-500">Prevent a Minecraft UUID from connecting to our server</div>
                    <hr class="my-6" />

                    <BanForm :submit="submit" />
                </div>
            </Card>
        </div>
    </section>
</template>
