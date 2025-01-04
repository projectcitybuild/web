<script setup lang="ts">
import { Head, InertiaForm } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import PlayerForm from './Partials/PlayerForm.vue'
import BackButton from '../../Components/BackButton.vue'
import type { Player } from '../../Data/Player'

function submit(form: InertiaForm<Player>) {
    form.post('/manage/players')
}
</script>

<template>
    <section>
        <Head title="Create a Player"/>

        <div class="mx-auto max-w-screen-xl">
            <Card>
                <div class="p-8 max-w-2xl">
                    <BackButton href="/manage/players" class="mb-4"/>

                    <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Create a Player</h2>
                    <div class="text-sm text-gray-500">A Minecraft UUID that has connected to our server before</div>
                    <hr class="my-6"/>

                    <PlayerForm :submit="submit"/>
                </div>
            </Card>
        </div>
    </section>
</template>
