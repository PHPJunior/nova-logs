<template>
    <div>
        <heading class="mb-6">Log [ {{ $route.params.date }} ]</heading>

        <div class="flex mb-4">
            <div class="w-1/6">
                <card class="flex mr-1 rounded-none">
                    <div class="w-full p-4">
                       <div>
                           <div>
                               <div class="font-bold text-xl mb-2">Levels</div>
                           </div>
                       </div>

                        <ul class="list-reset">
                            <li class="leading-wide text-sm" v-for="(menu, key) in menus" :key="key">
                                <button
                                    :disabled="menu.count === 0"
                                    :style="{ 'background-color' : key !== 'date' ? getColor(menu.count === 0 ? 'empty' : key) : '#1976D2' }"
                                    class="w-full font-bold py-2 px-4 inline-flex items-center"
                                    @click="$router.push({name: 'nova-log-viewer-show', params: {
                                        date: $route.params.date,
                                        level: key
                                    }})"
                                >
                                    <span class="text-white">{{ menu.name }}</span>
                                    <span class="flex rounded-full text-white uppercase px-2 py-1 text-xs font-bold mr-3">{{ menu.count }}</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                </card>
            </div>
            <div class="w-5/6">
                <card class="flex rounded-none" v-if="info != null">
                    <div class="w-full p-4">
                        <div>
                            <div class="float-left">
                                <div class="font-bold text-xl mb-2">Log info</div>
                            </div>

                            <div class="float-right">
                                <div class="inline-flex">
                                    <a :href="downloadUrl + log.date" target="_blank" class="bg-success font-bold py-2 px-4 rounded-l inline-flex items-center">
                                        <icon class="fill-current w-4 h-4 text-white" type="download" view-box="0 0 24 24" />
                                    </a>
                                    <button @click="openDeleteModal(log)" class="bg-danger font-bold py-2 px-4 rounded-r inline-flex items-center">
                                        <icon class="fill-current w-4 h-4 text-white" type="delete" viewBox="0 0 20 20" />
                                    </button>
                                </div>
                            </div>
                        </div>
                        <table class="w-full">
                            <tbody class="align-baseline">
                            <tr>
                                <td style="width: 23%;" class="text-sm pl-0 p-2">File Path :</td>
                                <td>{{ info.path }}</td>
                            </tr>
                            <tr>
                                <td style="width: 23%;" class="text-sm pl-0 p-2">Log Entries :</td>
                                <td>{{ info.entries }}</td>
                            </tr>
                            <tr>
                                <td style="width: 23%;" class="text-sm pl-0 p-2">Size :</td>
                                <td>{{ info.size }}</td>
                            </tr>
                            <tr>
                                <td style="width: 23%;" class="text-sm pl-0 p-2">Created At :</td>
                                <td>{{ info.created_at }}</td>
                            </tr>
                            <tr>
                                <td style="width: 23%;" class="text-sm pl-0 p-2">Updated At :</td>
                                <td>{{ info.updated_at }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </card>

                <card class="flex rounded-none mt-1 p-2">
                    <table class="table w-full border-0">
                        <thead>
                        <tr>
                            <th>Logs</th>
                            <th> Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <log
                            :entry="entry"
                            v-for="(entry, key) in entries"
                            :key="key"
                            @stack="openStackModal(entry)"
                        />
                        </tbody>
                    </table>
                </card>

                <card class="flex rounded-none justify-end" v-if="prev != null || next != null">
                    <div class="inline-flex">
                        <button @click="getLog(prev)" v-if="prev != null" class="bg-grey-light hover:bg-grey text-grey-darkest font-bold py-2 px-4 rounded-l">
                            Prev
                        </button>
                        <button @click="getLog(next)" v-if="next != null" class="bg-grey-light hover:bg-grey text-grey-darkest font-bold py-2 px-4 rounded-r">
                            Next
                        </button>
                    </div>
                </card>
            </div>
        </div>

        <portal to="modals">
            <transition name="fade">
                <delete-resource-modal
                    v-if="deleteModalOpen"
                    @confirm="confirmDelete"
                    @close="closeDeleteModal"
                    mode="delete"
                >
                    <div class="p-8">
                        <heading :level="2" class="mb-6">
                            Delete Log File
                        </heading>
                        <p class="text-80 leading-normal">
                            Are you sure you want to delete this log file {{ deleting.date }}?
                        </p>
                    </div>
                </delete-resource-modal>

                <modal v-if="stackModalOpen">
                    <button type="button" @click="closeStackModal" class="btn text-danger float-right p-2">
                        X
                    </button>
                    <div style="width: 950px;height:600px;overflow: overlay;">
                        <div class="p-4 stack-content">
                            {{ stack }}
                        </div>
                    </div>
                </modal>
            </transition>
        </portal>
    </div>
</template>

<script>
import Log from './Log';
import color from '../../utils/color';

export default {
    name: 'Show',
    components: {
        Log,
    },
    data() {
        return {
            info: null,
            log: null,
            levels: null,
            level: null,
            entries: null,
            menus: null,
            deleteModalOpen: false,
            deleting: null,
            next: null,
            prev: null,

            stackModalOpen: false,
            stack: null,
        };
    },
    watch: {
        '$route.params.level': function(level) {
            this.getLog(`/nova-vendor/php-junior/nova-log-viewer/get/${this.$route.params.date}/${level}`)
        },
    },
    computed: {
        downloadUrl() {
            return '/nova-vendor/php-junior/nova-log-viewer/download/';
        },
    },
    mounted() {
        this.getLog();
    },
    methods: {
        getColor(level) {
            return color(level);
        },
        getLog(url) {
            url =
                url ||
                `/nova-vendor/php-junior/nova-log-viewer/get/${this.$route.params.date}/${
                    this.$route.params.level
                }`;
            axios.get(url).then(({ data }) => {
                this.log = data.log;
                this.levels = data.levels;
                this.level = data.level;
                this.entries = data.entries.data;
                this.menus = data.menus;
                this.prev = data.entries.prev_page_url;
                this.next = data.entries.next_page_url;
                this.info = data.info;
            });
        },
        openDeleteModal(log) {
            this.deleteModalOpen = true;
            this.deleting = log;
        },

        closeDeleteModal() {
            this.deleteModalOpen = false;
            this.deleting = null;
        },

        openStackModal(entry) {
            this.stackModalOpen = true;
            this.stack = entry.stack;
        },

        closeStackModal() {
            this.stackModalOpen = false;
            this.stack = null;
        },

        confirmDelete() {
            axios({
                method: 'delete',
                url: '/nova-vendor/php-junior/nova-log-viewer/delete',
                data: {
                    date: this.deleting.date,
                },
            }).then(res => {
                this.deleteModalOpen = false;
                this.$router.push({ name: 'nova-log-viewer-list' });
            });
        },
    },
};
</script>

<style scoped>
.stack-content {
    color: #ae0e0e;
    font-family: consolas, Menlo, Courier, monospace;
    font-size: 12px;
    font-weight: 400;
    white-space: pre-line;
    width: 100%;
}
</style>
