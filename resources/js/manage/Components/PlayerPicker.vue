<script setup lang="ts">
import { computed, ref } from 'vue'
import axios from 'axios'
import Spinner from './Spinner.vue'

interface SelectedPlayer {
    uuid: string,
    alias: string,
}

interface Props {
    initialPlayer?: SelectedPlayer,
}

const props = defineProps<Props>()
const emit = defineEmits<{
    (e: 'uuidChange', uuid?: string): void,
}>()

const player = ref(props.initialPlayer)
const loading = ref(false)
const loadError = ref<string|null>(null)
const searchText = ref('')

const avatar = computed(() => `https://minotar.net/avatar/${player.value.uuid}/100`)

async function search(): Promise<void> {
    if (searchText.value == '') return

    loading.value = true
    loadError.value = null
    try {
        const response = await axios.get(`https://playerdb.co/api/player/minecraft/${searchText.value}`)
        const data = response.data.data.player
        if (data) {
            player.value = {
                uuid: data.id,
                alias: data.username,
            }
            emit('uuidChange', data.id)
        }
    } catch (error) {
        loadError.value = error.response.data.message ?? error.message
        emit('uuidChange', null)
    } finally {
        loading.value = false
    }
}

function clear(): void {
    player.value = null
}
</script>

<template>
    <div v-if="player" class="flex flex-row flex-wrap justify-between gap-5 items-center p-4 border border-gray-100 rounded-lg">
        <img :src="avatar" height="64" width="64" />

        <div class="grow">
            <div class="text-lg text-gray-900 dark:text-white font-bold">{{ player['alias'] }}</div>
            <div class="text-xs text-gray-400 dark:text-white">{{ player['uuid'] }}</div>
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
                required=""
                :disabled="loading"
                v-model="searchText"
            >

            <button
                type="button"
                class="px-5 py-2.5 text-sm text-center text-white bg-gray-500 rounded-lg focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900 hover:bg-gray-800"
                @click="search"
            >
                <Spinner v-if="loading" />
                <span v-else>Search</span>
            </button>
        </div>
    </div>
</template>
