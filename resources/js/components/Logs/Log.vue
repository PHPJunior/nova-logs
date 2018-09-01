<template>
    <tr>
        <td class="text-center" v-for="(value, key) in log" :key="key">
            <button :disabled="value === 0" type="button" @click="$router.push({name: 'nova-log-viewer-show', params: {
                date: date,
                level: key === 'date' ? 'all' : key
            }})" :style="{ 'background-color' : key !== 'date' ? getColor(value === 0 ? 'empty' : key) : '#1976D2' }"  class="inline-block p-1 text-sm font-semibold text-white mr-2">
                {{ value }}
            </button>
        </td>
        <td>
            <router-link :to="{name: 'nova-log-viewer-show', params: {
                date: date,
                level: 'all'
            }}"
                class="appearance-none mr-3 text-70 hover:text-info"
            >
                <icon type="view" width="22" height="18" view-box="0 0 22 16" />
            </router-link>
            <a
                :href="downloadUrl + date"
                target="_blank"
                rel="noopener nofollow"
                title="Download"
                class="appearance-none cursor-pointer text-70 hover:text-primary mr-3"
            >
                <icon type="download" view-box="0 0 24 24" width="20" height="20" />
            </a>
            <button
                title="Delete"
                class="appearance-none mr-3 text-70 hover:text-danger"
                @click.prevent="$emit('delete')"
            >
                <icon type="delete" />
            </button>
        </td>
    </tr>
</template>

<script>

    import color from '../../utils/color'
    export default {
        name: "Log",
        props: {
            date: { required: true },
            log: { required: true }
        },
        computed: {
            downloadUrl() {
                return '/nova-vendor/php-junior/nova-log-viewer/download/'
            },
        },
        methods: {
            getColor (level) {
                return color(level)
            }
        }
    }
</script>

<style scoped>

</style>
