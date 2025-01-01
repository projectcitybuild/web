<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import BackButton from '../../Components/BackButton.vue'
import type { Group } from '../../Data/Group'
import GroupMemberListTable from './Partials/GroupMemberListTable.vue'
import InfinitePagination from '../../Components/InfinitePagination.vue'

interface Props {
    group: Group,
}

defineProps<Props>()
</script>

<template>
    <div class="p-3 sm:p-5 mx-auto max-w-screen-xl">
        <Head :title="'Viewing Group: ' + group.name"/>

        <section>
            <Card class="mb-4">
                <div class="flex flex-row items-center justify-between p-4">
                    <BackButton href="/manage/groups"/>

                    <Link
                        :href="'/manage/groups/' + group.group_id + '/edit'"
                        as="button"
                        class="
                                flex flex-row items-center justify-center gap-2 px-4 py-2 rounded-lg
                                text-sm text-white bg-blue-700
                                hover:bg-primary-800
                                focus:ring-4 focus:ring-blue-300
                                dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800
                            "
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"/>
                        </svg>
                        Edit Group
                    </Link>
                </div>
            </Card>
        </section>

        <div class="flex flex-col gap-4 md:flex-row">
            <section>
                <Card class="p-8 max-w-2xl">
                    <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">{{ group.name }}</h2>
                    <hr class="my-6"/>

                    <div class="mt-4 space-y-6">
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <dl class="flex items-center justify-between gap-4">
                                    <dt class="text-gray-500 dark:text-gray-400">Alias</dt>
                                    <dd class="text-base font-medium text-gray-900 dark:text-white">
                                        {{ group.alias }}
                                    </dd>
                                </dl>
                                <dl class="flex items-center justify-between gap-4">
                                    <dt class="text-gray-500 dark:text-gray-400">Group Type</dt>
                                    <dd class="text-base font-medium text-gray-900 dark:text-white">
                                        {{ group.group_type }}
                                    </dd>
                                </dl>
                                <dl class="flex items-center justify-between gap-4">
                                    <dt class="text-gray-500 dark:text-gray-400">Minecraft Name</dt>
                                    <dd class="text-base font-medium text-gray-900 dark:text-white">
                                        {{ group.minecraft_name }}
                                    </dd>
                                </dl>
                                <dl class="flex items-center justify-between gap-4">
                                    <dt class="text-gray-500 dark:text-gray-400">Minecraft Display Name</dt>
                                    <dd class="text-base font-medium text-gray-900 dark:text-white">
                                        {{ group.minecraft_display_name }}
                                    </dd>
                                </dl>
                                <dl class="flex items-center justify-between gap-4">
                                    <dt class="text-gray-500 dark:text-gray-400">Minecraft Hover Text</dt>
                                    <dd class="text-base font-medium text-gray-900 dark:text-white">
                                        {{ group.minecraft_hover_text }}
                                    </dd>
                                </dl>
                                <dl class="flex items-center justify-between gap-4">
                                    <dt class="text-gray-500 dark:text-gray-400">Display Priority</dt>
                                    <dd class="text-base font-medium text-gray-900 dark:text-white">
                                        {{ group.display_priority }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </Card>
            </section>

            <section class="grow">
                <Card>
                    <div class="p-4 font-bold">
                        Group Members
                    </div>

                    <InfinitePagination
                        :path="'/manage/groups/' + group.group_id + '/accounts'"
                        v-slot="source"
                        class="overflow-x-auto"
                    >
                        <GroupMemberListTable :accounts="source.data"/>
                    </InfinitePagination>
                </Card>
            </section>
        </div>
    </div>
</template>
