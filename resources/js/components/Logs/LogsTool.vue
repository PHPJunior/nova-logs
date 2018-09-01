<template>
    <div>
        <heading class="mb-6">Nova Log Viewer</heading>

        <card class="flex flex-col items-center justify-center">
            <table class="table w-full" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th v-for="(header, key) in headers" :key="key">
                            <span :style="{ 'background-color' : key !== 'date' ? getColor(key) : '#1976D2' }" class="inline-block p-1  text-sm text-white">
                                {{ header }}
                            </span>
                        </th>
                        <th>

                        </th>
                    </tr>
                </thead>
                <tbody>
                    <log
                        :log="log"
                        :date="date"
                        v-for="(log, date) in logs"
                        :key="date"
                        @delete="openDeleteModal(log)">
                    </log>
                </tbody>
            </table>
        </card>

        <card class="flex mt-6 justify-end" v-if="prev != null || next != null">
            <div class="inline-flex">
                <button @click="getListLogs(prev)" v-if="prev != null" class="bg-grey-light hover:bg-grey text-grey-darkest font-bold py-2 px-4 rounded-l">
                    Prev
                </button>
                <button @click="getListLogs(next)" v-if="next != null" class="bg-grey-light hover:bg-grey text-grey-darkest font-bold py-2 px-4 rounded-r">
                    Next
                </button>
            </div>
        </card>

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
            </transition>
        </portal>
    </div>
</template>

<script>
import axios from 'axios';
import Log from './Log';
import color from '../../utils/color'

export default {
    data() {
        return {
            headers: null,
            rows: null,
            logs: null,
            deleteModalOpen: false,
            deleting: null,
            next: null,
            prev: null,
        };
    },
    components: {
        Log,
    },
    mounted() {
        this.getListLogs();
    },
    methods: {
        getColor (level) {
            return color(level)
        },
        async getListLogs(url) {
            url = url || '/nova-vendor/php-junior/nova-log-viewer/get_list_logs';
            const { data } = await axios.get(url);
            this.headers = data.headers;
            this.rows = data.rows;
            this.logs = data.rows.data;
            this.prev = data.rows.prev_page_url;
            this.next = data.rows.next_page_url;
        },

        openDeleteModal(log) {
            this.deleteModalOpen = true;
            this.deleting = log;
        },

        closeDeleteModal() {
            this.deleteModalOpen = false;
            this.deleting = null;
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
                delete this.logs[this.deleting.date];
            });
        },
    },
};
</script>

<style scoped>
</style>
