<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import BanForm from "./Partials/BanForm.vue";

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
                    <Link href="/manage/player-bans" class="flex flex-row items-center gap-2 mb-4 text-sm text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75 3 12m0 0 3.75-3.75M3 12h18" />
                        </svg>
                        Go back
                    </Link>

                    <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Create a Ban</h2>
                    <div class="text-sm text-gray-500">Prevent a Minecraft UUID from connecting to our server</div>
                    <hr class="my-6" />

                    <BanForm :submit="submit" />
                </div>
            </Card>
        </div>
    </section>
</template>
