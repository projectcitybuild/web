<script setup lang="ts">
import { computed, ref } from 'vue'
import { Result } from 'typescript-result'
import Spinner from './Spinner.vue'

interface Props {
    alias: string,
    size?: number,
}

const props = defineProps<Props>()

const loaded = ref<Result<void|null, Error>>(null)

const size = computed(() => props.size ?? 100)
const url = computed(() => `https://minotar.net/avatar/${props.alias}/${props.size}`)

function didLoad() {
    loaded.value = Result.ok()
}

function didFail(error: Error) {
    loaded.value = Result.error(error)
}

</script>

<template>
    <div
        v-if="loaded == null"
        class="bg-gray-50 rounded-lg flex justify-center items-center"
        :style="{width: props.size + 'px', height: props.size + 'px'}"
    >
        <Spinner  />
    </div>
    <div v-show="loaded?.isOk()">
        <img
            :src="url"
            :width="size"
            @load="didLoad"
            @error="didFail"
            :alt="props.alias"
        />
    </div>
    <div
        v-if="loaded?.isError()"
        class="bg-gray-50 rounded-lg flex justify-center items-center"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
        </svg>
    </div>
</template>
