<template>
    <tr class="group">
        <td class="border-t border-gray-100 dark:border-gray-700 px-2 py-2 pl-5 pr-5 dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
            <p>
                <span :style="{'background-color' : getColor(entry.level) }"
                      class="inline-block px-3 py-1 text-sm text-white font-semibold mr-3">
                {{ entry.level.toUpperCase() }}
                </span>

                Time : {{ entry.datetime }}
            </p>
            <p class="mt-2">
                {{ entry.header }}
            </p>
        </td>
        <td class="border-t border-gray-100 dark:border-gray-700 px-2 py-2 pl-5 pr-5 dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
            <button
                v-if="entry.stack.length > 2"
                class="bg-transparent hover:bg-primary text-primary border-primary font-semibold hover:text-white py-2 px-4 border hover:border-transparent rounded"
                @click.prevent="$emit('stack')"
            >
                Stack
            </button>
        </td>
    </tr>
</template>
co
<script>
import color from '../utils/color'

export default {
    name: 'ShowLogsRow',
    props: ['entry'],
    methods: {
        getColor(level) {
            return color(level)
        }
    }
}
</script>

<style scoped>

</style>
