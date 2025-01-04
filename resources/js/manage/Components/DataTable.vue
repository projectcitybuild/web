<script setup lang="ts" generic="T">
import { useSlots } from 'vue'

interface Field {
    key: string,
    label: string,
}

interface Props {
    fields: Field[],
    rows: T[],
    showIndex?: boolean,
}
withDefaults(defineProps<Props>(), {
    showIndex: false,
})

const slots = useSlots()
</script>

<template>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-500 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th
                        v-if="showIndex"
                        scope="col"
                        class="px-4 py-3"
                    >
                        #
                    </th>
                    <th
                        v-for="(field, index) in fields"
                        :key="index"
                        scope="col"
                        class="px-4 py-3"
                    >
                        {{ field.label }}
                    </th>
                    <th
                        v-if="slots.actions"
                        scope="col"
                        class="px-4 py-3"
                    >
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    class="border-b last:border-b-0 border-gray-50 dark:border-gray-700"
                    v-for="(row, index) in rows"
                    :key="index"
                >
                    <td
                        v-if="showIndex"
                        class="px-4 py-3 text-gray-300 whitespace-nowrap"
                    >
                        #{{ index + 1 }}
                    </td>

                    <td
                        class="px-4 py-3 text-gray-500 whitespace-nowrap dark:text-white"
                        v-for="field in fields"
                    >
                        <template v-if="slots[field.key]">
                            <slot :name="field.key" :item="row" />
                        </template>
                        <span v-else>
                            {{ row[field.key] }}
                        </span>
                    </td>

                    <td
                        v-if="slots.actions"
                        class="px-4 py-2 text-gray-900 whitespace-nowrap dark:text-white"
                    >
                        <slot name="actions" :item="row" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
