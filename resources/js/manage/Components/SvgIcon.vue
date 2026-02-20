<script setup lang="ts">
export type StrokeLineCap = "butt" | "round" | "square";
export type StrokeLineJoin = "miter" | "round" | "bevel";

export interface SvgPath {
    d: string
    strokeLineCap?: StrokeLineCap
    strokeLineJoin?: StrokeLineJoin
}

export interface Svg {
    fill?: string
    paths: SvgPath[]
}

interface Props {
    svg: Svg
    thickness?: number
    size?: string
}
const props = defineProps<Props>()
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        :stroke="svg.fill ? 'none' : 'currentColor'"
        :fill="svg.fill ?? 'none'"
        :stroke-width="thickness ?? 1.5"
        :class="['size-4', $attrs.class]"
    >
        <path v-for="path in svg.paths"
            :stroke-linecap="path.strokeLineCap"
            :stroke-linejoin="path.strokeLineJoin"
            :d="path.d"
        />
    </svg>
</template>
