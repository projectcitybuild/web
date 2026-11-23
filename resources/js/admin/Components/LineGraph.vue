<script setup lang="ts">
import { computed, ref } from 'vue'
import { format } from '../Utilities/DateFormatter'

type SeriesColor = 'success' | 'danger'
type SeriesValue = { timestamp: string; value: number }
type GraphSeries = {
    key: string
    label: string
    color: SeriesColor
    values: SeriesValue[]
    hideWhenEmpty?: boolean
}
type Point = { x: number; y: number; value: number; timestamp: string }

const props = defineProps<{
    series: GraphSeries[]
    label: string
}>()

const hiddenSeries = ref<string[]>([])
const activeTooltip = ref<{ series: string; index: number } | null>(null)
const plot = { left: 52, right: 744, top: 20, bottom: 224 }

function isVisible(series: GraphSeries): boolean {
    return !hiddenSeries.value.includes(series.key)
}

function toggleSeries(seriesKey: string): void {
    hiddenSeries.value = hiddenSeries.value.includes(seriesKey)
        ? hiddenSeries.value.filter((key) => key !== seriesKey)
        : [...hiddenSeries.value, seriesKey]
}

const visibleMaximum = computed(() => {
    const values = props.series
        .filter(isVisible)
        .flatMap((series) => series.values.map((point) => point.value))

    return Math.max(1, ...values)
})

const gridLines = computed(() => Array.from({ length: 5 }, (_, index) => {
    const fraction = index / 4
    const value = Math.round(visibleMaximum.value * (1 - fraction))

    return {
        y: plot.top + (plot.bottom - plot.top) * fraction,
        value,
    }
}))

function pointsFor(series: GraphSeries): Point[] {
    const width = plot.right - plot.left
    const height = plot.bottom - plot.top

    return series.values.map((point, index) => ({
        x: plot.left + (series.values.length <= 1 ? 0 : (index / (series.values.length - 1)) * width),
        y: plot.bottom - (point.value / visibleMaximum.value) * height,
        value: point.value,
        timestamp: point.timestamp,
    }))
}

function pathFor(points: Point[]): string {
    return points.map((point, index) => `${index === 0 ? 'M' : 'L'} ${point.x} ${point.y}`).join(' ')
}

const graphSeries = computed(() => props.series.map((series) => {
    const points = pointsFor(series)

    return {
        ...series,
        points,
        path: pathFor(points),
        total: series.values.reduce((total, point) => total + point.value, 0),
    }
}))
const visibleGraphSeries = computed(() => graphSeries.value.filter((series) => (
    isVisible(series) && (!series.hideWhenEmpty || series.total > 0)
)))
const tickIndexes = computed(() => {
    const lastIndex = (props.series[0]?.values.length ?? 0) - 1

    if (lastIndex <= 0) {
        return [0]
    }

    return Array.from(new Set([0, 0.25, 0.5, 0.75, 1].map(fraction => Math.round(lastIndex * fraction))))
})
const tickPoints = computed(() => graphSeries.value[0]?.points ?? [])
const hasData = computed(() => graphSeries.value.some((series) => series.total > 0))
const accessibleSummary = computed(() => graphSeries.value
    .map((series) => `${series.total} ${series.label.toLowerCase()}`)
    .join(' and '))
const tooltip = computed(() => {
    if (!activeTooltip.value) {
        return null
    }

    const series = graphSeries.value.find((item) => item.key === activeTooltip.value?.series)
    const point = series?.points[activeTooltip.value.index]

    if (!series || !point) {
        return null
    }

    return {
        x: Math.min(Math.max(point.x - 78, plot.left), plot.right - 156),
        y: Math.max(plot.top, point.y - 48),
        value: point.value,
        timestamp: format(point.timestamp),
        series: series.label,
    }
})

function tickLabel(index: number): string {
    const timestamp = props.series[0]?.values[index]?.timestamp

    return timestamp
        ? new Intl.DateTimeFormat(undefined, { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' }).format(new Date(timestamp))
        : ''
}
</script>

<template>
    <div class="space-y-4">
        <div class="flex flex-wrap justify-end gap-2">
            <button
                v-for="item in series"
                :key="item.key"
                type="button"
                class="inline-flex items-center gap-2 rounded-md px-2 py-1 text-sm text-foreground hover:bg-surface-hover focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus"
                :aria-pressed="isVisible(item)"
                @click="toggleSeries(item.key)"
            >
                <span
                    class="size-2.5 rounded-full"
                    :class="item.color === 'success' ? 'bg-success' : 'bg-danger'"
                    aria-hidden="true"
                />
                {{ item.label }}
            </button>
        </div>

        <p class="sr-only">{{ accessibleSummary }}</p>

        <div v-if="!hasData" class="flex min-h-64 flex-col items-center justify-center gap-2 rounded-lg border border-dashed border-border px-6 text-center">
            <h3 class="font-medium text-foreground">No completed jobs in this range</h3>
            <p class="max-w-md text-sm text-muted">Throughput appears here after tracked jobs complete.</p>
        </div>

        <div v-else class="overflow-x-auto">
            <svg
                class="h-72 min-w-[36rem] w-full"
                viewBox="0 0 760 280"
                role="img"
                :aria-label="label"
            >
                <title>{{ label }}</title>

                <g v-for="line in gridLines" :key="line.y">
                    <line :x1="plot.left" :x2="plot.right" :y1="line.y" :y2="line.y" class="text-border" stroke="currentColor" stroke-dasharray="3 5" />
                    <text :x="plot.left - 10" :y="line.y + 4" text-anchor="end" class="fill-current text-[11px] text-muted">{{ line.value }}</text>
                </g>

                <g v-for="item in visibleGraphSeries" :key="item.key">
                    <path
                        :d="item.path"
                        fill="none"
                        :class="item.color === 'success' ? 'text-success' : 'text-danger'"
                        stroke="currentColor"
                        stroke-width="2.5"
                        stroke-linejoin="round"
                        stroke-linecap="round"
                    />
                    <circle
                        v-for="(point, index) in item.points"
                        :key="`${item.key}-${index}`"
                        :cx="point.x"
                        :cy="point.y"
                        r="3.5"
                        class="cursor-crosshair fill-current"
                        :class="item.color === 'success' ? 'text-success' : 'text-danger'"
                        tabindex="0"
                        :aria-label="`${point.value} ${item.label.toLowerCase()} at ${format(point.timestamp)}`"
                        @mouseenter="activeTooltip = { series: item.key, index }"
                        @mouseleave="activeTooltip = null"
                        @focus="activeTooltip = { series: item.key, index }"
                        @blur="activeTooltip = null"
                    >
                        <title>{{ point.value }} {{ item.label.toLowerCase() }} · {{ format(point.timestamp) }}</title>
                    </circle>
                </g>

                <g v-for="index in tickIndexes" :key="`tick-${index}`">
                    <text :x="tickPoints[index]?.x ?? plot.left" :y="plot.bottom + 25" text-anchor="middle" class="fill-current text-[11px] text-muted">
                        {{ tickLabel(index) }}
                    </text>
                </g>

                <g v-if="tooltip" aria-hidden="true">
                    <rect :x="tooltip.x" :y="tooltip.y" width="156" height="40" rx="6" class="fill-surface stroke-border" stroke="currentColor" />
                    <text :x="tooltip.x + 8" :y="tooltip.y + 16" class="fill-current text-[11px] font-medium text-foreground">
                        {{ tooltip.series }}: {{ tooltip.value }}
                    </text>
                    <text :x="tooltip.x + 8" :y="tooltip.y + 31" class="fill-current text-[10px] text-muted">
                        {{ tooltip.timestamp }}
                    </text>
                </g>
            </svg>
        </div>
    </div>
</template>
