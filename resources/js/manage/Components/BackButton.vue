<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import SvgIcon from './SvgIcon.vue'
import { Icons } from '../Icons';

interface Props {
    href: string,
}

defineProps<Props>()
</script>

<template>
    <Link :href="href" class="flex flex-row items-center gap-2 text-sm text-gray-500">
        <SvgIcon :svg="Icons.arrow-left" class="size-6" />
        Go Back
    </Link>
</template>
