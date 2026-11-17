<script setup lang="ts">
import { computed, nextTick, onBeforeUnmount, ref, useId, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import SvgIcon from '../../../../manage/Components/SvgIcon.vue'
import { Icons } from '../../../../manage/Icons'

interface AccountInfo {
    name: string
    avatarUuid: string | null
}

interface PageProps {
    account: AccountInfo | null
}

const props = withDefaults(defineProps<{
    collapsed?: boolean
}>(), {
    collapsed: false,
})

const page = usePage<PageProps>()
const menuId = useId()
const root = ref<HTMLDivElement | null>(null)
const trigger = ref<HTMLButtonElement | null>(null)
const menu = ref<HTMLDivElement | null>(null)
const isOpen = ref(false)
const avatarFailed = ref(false)
const menuPosition = ref({ left: '0px', top: '0px' })
const accountName = computed(() => page.props.account?.name ?? 'Account')
const avatarUrl = computed(() => page.props.account?.avatarUuid
    ? `https://minotar.net/avatar/${encodeURIComponent(page.props.account.avatarUuid)}`
    : null)

watch(avatarUrl, () => {
    avatarFailed.value = false
})

function updateMenuPosition(): void {
    const triggerBounds = trigger.value?.getBoundingClientRect()
    const menuBounds = menu.value?.getBoundingClientRect()

    if (!triggerBounds || !menuBounds) {
        return
    }

    const gap = 8
    const preferredLeft = props.collapsed
        ? triggerBounds.right + gap
        : triggerBounds.right - menuBounds.width
    const maxLeft = Math.max(gap, window.innerWidth - menuBounds.width - gap)
    const left = Math.min(Math.max(preferredLeft, gap), maxLeft)
    const preferredTop = triggerBounds.top - menuBounds.height - gap
    const maxTop = Math.max(gap, window.innerHeight - menuBounds.height - gap)
    const top = Math.min(Math.max(preferredTop, gap), maxTop)

    menuPosition.value = {
        left: `${left}px`,
        top: `${top}px`,
    }
}

function menuItems(): HTMLAnchorElement[] {
    return Array.from(menu.value?.querySelectorAll<HTMLAnchorElement>('[role="menuitem"]') ?? [])
}

function openMenu(): void {
    isOpen.value = true
    void nextTick(() => {
        updateMenuPosition()
        menuItems()[0]?.focus()
    })
}

function closeMenu(restoreFocus = false): void {
    isOpen.value = false

    if (restoreFocus) {
        void nextTick(() => trigger.value?.focus())
    }
}

function toggleMenu(): void {
    if (isOpen.value) {
        closeMenu()
        return
    }

    openMenu()
}

function onDocumentPointerDown(event: PointerEvent): void {
    if (!(event.target instanceof Node)) {
        return
    }

    if (!root.value?.contains(event.target) && !menu.value?.contains(event.target)) {
        closeMenu()
    }
}

function onMenuKeydown(event: KeyboardEvent): void {
    const items = menuItems()
    const activeIndex = items.findIndex(item => item === document.activeElement)

    if (event.key === 'Escape') {
        event.preventDefault()
        closeMenu(true)
        return
    }

    if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
        if (items.length === 0) {
            return
        }

        event.preventDefault()
        const offset = event.key === 'ArrowDown' ? 1 : -1
        const nextIndex = (activeIndex + offset + items.length) % items.length
        items[nextIndex]?.focus()
        return
    }

    if (event.key === 'Home') {
        event.preventDefault()
        items[0]?.focus()
        return
    }

    if (event.key === 'End') {
        event.preventDefault()
        items.at(-1)?.focus()
    }
}

function onMenuFocusOut(event: FocusEvent): void {
    const nextTarget = event.relatedTarget

    if (
        !(nextTarget instanceof Node)
        || (!menu.value?.contains(nextTarget) && !root.value?.contains(nextTarget))
    ) {
        closeMenu()
    }
}

watch(isOpen, open => {
    if (open) {
        document.addEventListener('pointerdown', onDocumentPointerDown)
        window.addEventListener('resize', updateMenuPosition)
        window.addEventListener('scroll', updateMenuPosition, true)
        return
    }

    document.removeEventListener('pointerdown', onDocumentPointerDown)
    window.removeEventListener('resize', updateMenuPosition)
    window.removeEventListener('scroll', updateMenuPosition, true)
})

onBeforeUnmount(() => {
    document.removeEventListener('pointerdown', onDocumentPointerDown)
    window.removeEventListener('resize', updateMenuPosition)
    window.removeEventListener('scroll', updateMenuPosition, true)
})
</script>

<template>
    <div
        ref="root"
        :class="props.collapsed ? 'relative flex w-full justify-center' : 'relative min-w-0 w-full'"
    >
        <button
            ref="trigger"
            type="button"
            :aria-label="`Open account menu for ${accountName}`"
            :aria-expanded="isOpen"
            aria-haspopup="menu"
            :aria-controls="menuId"
            :title="props.collapsed ? accountName : undefined"
            :class="[
                'group flex items-center rounded-md text-left text-foreground transition-colors hover:bg-surface-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus',
                props.collapsed ? 'relative size-10 justify-center' : 'min-h-11 w-full gap-3 px-2 py-1.5',
            ]"
            @click="toggleMenu"
        >
            <span
                :class="[
                    'flex shrink-0 items-center justify-center overflow-hidden rounded-full bg-surface-hover text-muted',
                    props.collapsed ? 'size-8' : 'size-9',
                ]"
            >
                <img
                    v-if="avatarUrl && !avatarFailed"
                    :src="avatarUrl"
                    :alt="accountName"
                    class="size-full object-cover"
                    @error="avatarFailed = true"
                />
                <svg
                    v-else
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    class="size-6"
                    aria-hidden="true"
                >
                    <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-5.5-2.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0ZM10 12a5.99 5.99 0 0 0-4.793 2.39A6.483 6.483 0 0 0 10 16.5a6.483 6.483 0 0 0 4.793-2.11A5.99 5.99 0 0 0 10 12Z" clip-rule="evenodd" />
                </svg>
            </span>

            <span v-if="!props.collapsed" class="min-w-0 flex-1">
                <span class="block truncate text-sm font-semibold">{{ accountName }}</span>
            </span>

            <span
                v-if="props.collapsed"
                class="absolute bottom-0 right-0 flex size-4 items-center justify-center rounded-full bg-surface text-muted shadow-sm"
                aria-hidden="true"
            >
                <SvgIcon :svg="Icons.moreHorizontal" class="size-3" />
            </span>
            <SvgIcon
                v-else
                :svg="Icons.moreHorizontal"
                class="size-5 shrink-0 text-muted transition-colors group-hover:text-foreground"
                aria-hidden="true"
            />
        </button>

        <Teleport to="body">
            <div
                v-if="isOpen"
                :id="menuId"
                ref="menu"
                role="menu"
                aria-label="Account menu"
                class="fixed z-50 max-h-[calc(100vh-1rem)] w-56 max-w-[calc(100vw-1rem)] overflow-y-auto rounded-lg border border-border bg-surface py-1 text-foreground shadow-xl"
                :style="menuPosition"
                @keydown="onMenuKeydown"
                @focusout="onMenuFocusOut"
            >
                <div class="border-b border-border px-3 py-2">
                    <p class="truncate text-sm font-semibold">{{ accountName }}</p>
                </div>
                <a
                    href="/"
                    role="menuitem"
                    tabindex="-1"
                    class="flex min-h-10 items-center px-3 text-sm text-muted transition-colors hover:bg-surface-hover hover:text-foreground focus-visible:bg-surface-hover focus-visible:outline-none"
                    @click="closeMenu()"
                >
                    Back to main website
                </a>
                <a
                    href="/account/settings"
                    role="menuitem"
                    tabindex="-1"
                    class="flex min-h-10 items-center px-3 text-sm text-muted transition-colors hover:bg-surface-hover hover:text-foreground focus-visible:bg-surface-hover focus-visible:outline-none"
                    @click="closeMenu()"
                >
                    Settings
                </a>
            </div>
        </Teleport>
    </div>
</template>
