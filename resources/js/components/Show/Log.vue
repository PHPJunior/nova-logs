<template>
    <tr>
        <td class="p-2">
            <p>
                <span :style="{'background-color' : getColor(entry.level) }" class="inline-block px-3 py-1 text-sm text-white font-semibold mr-3">
                {{ entry.level.toUpperCase() }}
                </span>

                Time : {{ entry.datetime }}
            </p>
            <p class="mt-2">
                {{ entry.header }}
            </p>
        </td>
        <td class="p-2">
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
    import color from '../../utils/color'
    export default {
        name: "Log",
        props: ['entry'],
        methods : {
            getColor (level) {
                return color(level)
            }
        }
    }
</script>

<style scoped>

</style>
