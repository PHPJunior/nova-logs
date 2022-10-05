<template>
    <tr class="group">
        <td :class="rowClasses" v-for="(value, key) in log" :key="key">
            <Link :disabled="value === 0" type="button" :href="`${baseUrl}/list/${date}/${level(key)}`"
                  :style="{ 'background-color' : key !== 'date' ? getColor(value === 0 ? 'empty' : key) : '#1976D2' }"
                  class="inline-block p-1 text-sm font-semibold text-white mr-2">
                {{ value }}
            </Link>
        </td>
        <td :class="rowClasses">
            <Link :href="`${baseUrl}/list/${date}/all`"
                  class="appearance-none mr-3 text-70 hover:text-info"
            >
                <icon class="text-gray-500 dark:text-gray-400" type="eye"/>
            </Link>
            <Link
                :href="downloadUrl + date"
                target="_blank"
                rel="noopener nofollow"
                title="Download"
                class="appearance-none cursor-pointer text-70 hover:text-primary mr-3"
            >
                <icon class="text-gray-500 dark:text-gray-400" type="download"/>
            </Link>
            <button
                title="Delete"
                class="appearance-none mr-3 text-70 hover:text-danger"
                @click.prevent="$emit('delete')"
            >
                <icon class="text-gray-500 dark:text-gray-400" type="trash"/>
            </button>
        </td>
    </tr>
</template>

<script>
import color from '../utils/color'

export default {
    name: 'LogsToolRow',
    props: {
        date: {required: true},
        log: {required: true},
        baseUrl: {required: true},
    },
    computed: {
        downloadUrl() {
            return '/nova-vendor/php-junior/nova-log-viewer/download/'
        },
        rowClasses: () => 'td-fit border-t border-gray-100 dark:border-gray-700 px-2 py-2 pl-5 pr-5 dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900 text-center',
    },
    methods: {
        getColor(level) {
            return color(level)
        },
        level(key) {
            return key === 'date' ? 'all' : key;
        }
    }
}
</script>
