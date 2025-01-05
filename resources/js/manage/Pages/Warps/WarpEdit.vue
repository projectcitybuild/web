<script setup lang="ts">
import { Head, InertiaForm } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import WarpForm from './Partials/WarpForm.vue'
import BackButton from '../../Components/BackButton.vue'
import type { Warp } from '../../Data/Warp'

interface Props {
    warp: Warp,
}

const props = defineProps<Props>()

function submit(form: InertiaForm<Warp>) {
    form.put('/manage/minecraft/warps/' + props.warp.id)
}
</script>

<template>
    <section>
        <Head title="Edit a Warp"/>

        <Card>
            <div class="p-8 max-w-2xl">
                <BackButton href="/manage/minecraft/warps" class="mb-4"/>

                <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Edit a Warp</h2>
                <div class="text-sm text-gray-500">Register a location players can freely teleport to</div>
                <hr class="my-6"/>

                <WarpForm :warp="props.warp" :submit="submit"/>
            </div>
        </Card>
    </section>
</template>
