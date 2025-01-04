<script setup>
import SideBar from './Partials/SideBar.vue'
import NavBar from './Partials/NavBar.vue'
</script>

<template>
    <div>
        <NavBar/>

        <SideBar/>

        <main class="p-4 md:p-8 md:ml-64 h-auto mt-16">
            <slot/>
        </main>
    </div>
</template>
