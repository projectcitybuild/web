<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <main>
        <header>
            <Link href="/manage">Home</Link>
            <Link href="/manage/player-bans">Bans</Link>
        </header>
        <article>
            <slot />
        </article>
    </main>
</template>
