<script setup lang="ts">
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue'

interface Tab {
    id: string,
    label: string,
}

interface Props {
    tabs: readonly Tab[],
    idPrefix: string,
    ariaLabel: string,
}

const props = defineProps<Props>()
const activeTab = defineModel<string>({ required: true })
const tabNav = ref<HTMLElement>()
const tabButtons = ref<HTMLButtonElement[]>([])
const tabIndicatorStyle = ref({ width: '0px', transform: 'translateX(0px)' })
let tabResizeObserver: ResizeObserver | undefined

function updateTabIndicator() {
    const activeTabIndex = props.tabs.findIndex((tab) => tab.id === activeTab.value)
    const activeButton = tabButtons.value[activeTabIndex]

    if (!activeButton) {
        return
    }

    tabIndicatorStyle.value = {
        width: activeButton.offsetWidth + 'px',
        transform: 'translateX(' + activeButton.offsetLeft + 'px)',
    }
}

function handleKeydown(event: KeyboardEvent, currentIndex: number) {
    let nextIndex: number | undefined

    if (event.key === 'ArrowRight') {
        nextIndex = (currentIndex + 1) % props.tabs.length
    } else if (event.key === 'ArrowLeft') {
        nextIndex = (currentIndex - 1 + props.tabs.length) % props.tabs.length
    } else if (event.key === 'Home') {
        nextIndex = 0
    } else if (event.key === 'End') {
        nextIndex = props.tabs.length - 1
    }

    if (nextIndex === undefined) {
        return
    }

    event.preventDefault()
    const nextTab = props.tabs[nextIndex]

    if (nextTab) {
        activeTab.value = nextTab.id
        tabButtons.value[nextIndex]?.focus()
    }
}

watch(activeTab, () => {
    void nextTick(updateTabIndicator)
})

onMounted(() => {
    void nextTick(updateTabIndicator)

    if (tabNav.value) {
        tabResizeObserver = new ResizeObserver(updateTabIndicator)
        tabResizeObserver.observe(tabNav.value)
        tabButtons.value.forEach((button) => tabResizeObserver?.observe(button))
    }
})

onBeforeUnmount(() => tabResizeObserver?.disconnect())
</script>

<template>
    <div class="border-b border-border">
        <nav ref="tabNav" class="relative flex gap-6 overflow-x-auto" role="tablist" :aria-label="ariaLabel">
            <button
                v-for="(tab, index) in tabs"
                :key="tab.id"
                ref="tabButtons"
                type="button"
                role="tab"
                class="shrink-0 border-b-2 border-transparent px-1 pb-3 text-sm font-medium transition-colors"
                :class="activeTab === tab.id ? 'text-primary' : 'text-muted hover:text-foreground dark:text-subtle dark:hover:text-foreground'"
                :tabindex="activeTab === tab.id ? 0 : -1"
                :aria-selected="activeTab === tab.id"
                :aria-controls="idPrefix + '-panel-' + tab.id"
                :id="idPrefix + '-tab-' + tab.id"
                @click="activeTab = tab.id"
                @keydown="handleKeydown($event, index)"
            >
                {{ tab.label }}
            </button>
            <span
                aria-hidden="true"
                class="pointer-events-none absolute bottom-0 left-0 h-0.5 bg-primary transition-[width,transform] duration-300 ease-in-out"
                :style="tabIndicatorStyle"
            />
        </nav>
    </div>
</template>
