<script setup lang="ts">
import { onMounted, ref } from 'vue'
import Spinner from './Spinner.vue'
import MinecraftAvatar from './MinecraftAvatar.vue'
import { lookupPlayer } from '../Services/PlayerLookupService'

interface Props {
    initialSearch?: string,
}
const props = defineProps<Props>()

const uuid = defineModel<string>('uuid')
const alias = defineModel<string>('alias')

const loading = ref(false)
const loadError = ref<string | null>(null)
const searchText = ref('')

async function search(): Promise<void> {
    if (searchText.value == '') return

    loading.value = true
    loadError.value = null
    try {
        const data = await lookupPlayer(searchText.value)
        uuid.value = data?.uuid
        alias.value = data?.alias
    } catch (error) {
        loadError.value = error.message
        uuid.value = null
        alias.value = null
    } finally {
        loading.value = false
    }
}

function clear(): void {
    uuid.value = null
    alias.value = null
}

onMounted(() => {
    if (props.initialSearch && props.initialSearch != '') {
        searchText.value = props.initialSearch
        search()
    }
})
</script>

<template>
    <div v-if="uuid"
         class="flex flex-row flex-wrap justify-between gap-5 items-center p-4 border border-gray-100 rounded-lg">
        <MinecraftAvatar :alias="alias" :size="64"/>

        <div class="grow">
            <div class="text-lg text-gray-900 dark:text-white font-bold">{{ alias }}</div>
            <div class="text-xs text-gray-400 dark:text-white">{{ uuid }}</div>
        </div>

        <button
            type="submit"
            class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-center border-2 border-gray-300 text-gray-400 rounded-lg focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900 hover:border-gray-400 hover:text-gray-600"
            @click="clear"
        >
            Clear
        </button>
    </div>

    <div v-else>
        <div v-if="loadError" class="text-red-500 text-sm font-bold mb-2">{{ loadError }}</div>

        <div class="flex flex-row gap-2 justify-center items-center">
            <input
                type="text"
                name="name"
                id="name"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="UUID or name"
                :disabled="loading"
                v-model="searchText"
            >

            <button
                type="button"
                class="px-5 py-2.5 text-sm text-center text-white bg-gray-500 rounded-lg focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900 hover:bg-gray-800"
                @click="search"
            >
                <Spinner v-if="loading"/>
                <span v-else>Search</span>
            </button>
        </div>
    </div>
</template>
