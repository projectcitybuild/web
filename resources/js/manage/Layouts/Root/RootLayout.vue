<script setup>
import NavBar from './Partials/NavBar.vue'
</script>

<template>
    <div>
        <NavBar />

        <slot name="side-bar" />

        <main class="mx-auto max-w-screen-xl p-4 md:p-8 md:ml-64 h-auto mt-16">
            <slot />
        </main>
    </div>
</template>
