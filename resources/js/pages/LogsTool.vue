<template>
    <div class="nova-logs">
        <div :class="{
            'dark': isDark,
        }">
            <heading class="mb-6">Nova Log Viewer</heading>

            <card class="flex flex-col items-center justify-center">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th v-for="(header, key) in headers" :key="key"
                            class="td-fit uppercase text-xxs text-gray-500 tracking-wide pl-5 pr-2 py-2">
                            <span :style="{ 'background-color' : key !== 'date' ? getColor(key) : '#1976D2' }"
                                  class="inline-block p-1  text-sm text-white">
                                {{ header }}
                            </span>
                        </th>
                        <th>

                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <LogsToolRow
                        :log="log"
                        :baseUrl="baseUrl"
                        :date="date"
                        v-for="(log, date) in logs"
                        :key="date"
                        @delete="openDeleteModal(log)">
                    </LogsToolRow>
                    </tbody>
                </table>
            </card>

            <card class="flex mt-6 justify-end" v-if="prev != null || next != null">
                <div class="inline-flex">
                    <button @click="getListLogs(prev)" v-if="prev != null"
                            class="bg-grey-light hover:bg-grey text-grey-darkest font-bold py-2 px-4 rounded-l">
                        Prev
                    </button>
                    <button @click="getListLogs(next)" v-if="next != null"
                            class="bg-grey-light hover:bg-grey text-grey-darkest font-bold py-2 px-4 rounded-r">
                        Next
                    </button>
                </div>
            </card>

            <DeleteResourceModal
                :show="deleteModalOpen"
                mode="delete"
                @close="closeDeleteModal"
                @confirm="confirmDelete"
            >
                <ModalHeader>Delete Log File</ModalHeader>
                <ModalContent>
                    <p class="text-80 leading-normal">
                        Are you sure you want to delete this log file {{ deleting.date }}?
                    </p>
                </ModalContent>
            </DeleteResourceModal>
        </div>
    </div>
</template>

<script>
import LogsToolRow from '../components/LogsToolRow';
import color from '../utils/color'
import dark from '../utils/dark'

export default {
    props: ['baseUrl'],
    data() {
        return {
            headers: null,
            rows: null,
            logs: null,
            deleteModalOpen: false,
            deleting: null,
            next: null,
            prev: null,
            isDark: false,
        };
    },
    components: {
        LogsToolRow,
    },
    mounted() {
        this.getListLogs();
        dark((isDark) => this.isDark = isDark);
    },
    methods: {
        getColor(level) {
            return color(level)
        },
        async getListLogs(url) {
            url = url || '/nova-vendor/php-junior/nova-log-viewer/get_list_logs';
            const {data} = await Nova.request().get(url);
            this.headers = data.headers;
            this.rows = data.rows;
            this.logs = data.rows.data;
            this.prev = data.rows.prev_page_url;
            this.next = data.rows.next_page_url;
        },

        openDeleteModal(log) {
            console.log('opening...');
            this.deleteModalOpen = true;
            this.deleting = log;
        },

        closeDeleteModal() {
            this.deleteModalOpen = false;
            this.deleting = null;
        },

        async confirmDelete() {
            await Nova.request().delete('/nova-vendor/php-junior/nova-log-viewer/delete', {
                data: {
                    date: this.deleting.date,
                }
            });

            this.deleteModalOpen = false;
            delete this.logs[this.deleting.date];
        },
    },
};
</script>

<style scoped>
</style>
