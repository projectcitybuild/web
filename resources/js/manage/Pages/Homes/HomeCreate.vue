<script setup lang="ts">
import { Head, InertiaForm } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import BackButton from '../../Components/BackButton.vue'
import { Warp } from '../../Data/Warp'
import HomeForm from './Partials/HomeForm.vue'

function submit(form: InertiaForm<Warp>) {
    form.post('/manage/homes')
}
</script>

<template>
    <section>
        <Head title="Create a Home"/>

        <Card>
            <div class="p-8 max-w-2xl">
                <BackButton href="/manage/homes" class="mb-4"/>

                <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Create a Home</h2>
                <div class="text-sm text-gray-500">Register a saved home coordinate for a player</div>
                <hr class="my-6"/>

                <HomeForm :submit="submit"/>
            </div>
        </Card>
    </section>
</template>
