<script setup>
import { Head } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import WarpForm from './Partials/WarpForm.vue'
import BackButton from '../../Components/BackButton.vue'

function submit(form) {
    form.post('/manage/minecraft/warps')
}
</script>

<template>
    <section class="p-3 sm:p-5">
        <Head title="Create a Warp" />

        <div class="mx-auto max-w-screen-xl">
            <Card>
                <div class="p-8 max-w-2xl">
                    <BackButton href="/manage/minecraft/warps" class="mb-4" />

                    <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Create a Warp</h2>
                    <div class="text-sm text-gray-500">Register a location players can freely teleport to</div>
                    <hr class="my-6" />

                    <WarpForm :submit="submit" />
                </div>
            </Card>
        </div>
    </section>
</template>
