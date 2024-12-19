<script setup>
import SideBar from "./Partials/SideBar.vue";
import NavBar from "./Partials/NavBar.vue";
</script>

<template>
    <div class="antialiased bg-gray-50 dark:bg-gray-900">
        <NavBar />

        <SideBar />

        <main class="p-4 md:ml-64 h-auto pt-16">
            <slot />
        </main>
    </div>
</template>
