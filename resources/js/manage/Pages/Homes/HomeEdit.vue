<script setup lang="ts">
import { Head, InertiaForm } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import BackButton from '../../Components/BackButton.vue'
import { Home } from '../../Data/Home';
import HomeForm from './Partials/HomeForm.vue';

interface Props {
    home: Home,
}

const props = defineProps<Props>()

function submit(form: InertiaForm<Home>) {
    form.put('/manage/homes/' + props.home.id)
}
</script>

<template>
    <section>
        <Head title="Edit a Warp"/>

        <Card>
            <div class="p-8 max-w-2xl">
                <BackButton href="/manage/homes" class="mb-4"/>

                <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Edit a Home</h2>
                <div class="text-sm text-gray-500">Register a saved home coordinate for a player</div>
                <hr class="my-6"/>

                <HomeForm :home="props.home" :submit="submit"/>
            </div>
        </Card>
    </section>
</template>
