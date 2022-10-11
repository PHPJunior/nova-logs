<template>
    <div class="nova-logs">
        <div :class="{
            'dark': isDark,
        }">
            <heading class="mb-6">Log [ {{ date }} ]</heading>

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
                                        :style="{ 'background-color' : key !== 'date' ? colors[menu.count === 0 ? 'empty' : key] : '#1976D2' }"
                                        class="w-full font-bold py-2 px-4 inline-flex items-center text-white"
                                        @click="visitLogByLevel(key)"
                                    >
                                        <icon v-if="key === currentLevel" type="check" class="h-4 w-4 mr-2" />
                                        <span v-else class="w-4 mr-2"></span>
                                        <span>{{ menu.name }}</span>
                                        <span
                                            class="flex rounded-full uppercase px-2 py-1 text-xs font-bold mr-3"
                                        >
                                            {{ menu.count }}
                                        </span>
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
                                        <a :href="downloadUrl + log.date" target="_blank"
                                           class="bg-green-100 text-green-600 dark:bg-green-500 dark:text-green-900 font-bold py-2 px-4 rounded-l inline-flex items-center"
                                        >
                                            <icon class="w-4 h-4" type="download"/>
                                        </a>
                                        <button @click="openDeleteModal(log)"
                                                class="bg-red-100 text-red-600 dark:bg-red-400 dark:text-red-900 font-bold py-2 px-4 rounded-r inline-flex items-center"
                                        >
                                            <icon class="w-4 h-4" type="trash"/>
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
                            <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="uppercase text-xxs text-gray-500 tracking-wide pl-5 pr-2 py-2">Logs</th>
                                <th class="uppercase text-xxs text-gray-500 tracking-wide pl-5 pr-2 py-2"> Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <ShowLogsRow
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
                            <button @click="getLog(prev)" v-if="prev != null"
                                    class="bg-grey-light hover:bg-grey text-grey-darkest font-bold py-2 px-4 rounded-l">
                                Prev
                            </button>
                            <button @click="getLog(next)" v-if="next != null"
                                    class="bg-grey-light hover:bg-grey text-grey-darkest font-bold py-2 px-4 rounded-r">
                                Next
                            </button>
                        </div>
                    </card>
                </div>
            </div>

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

            <Modal
                :show="stackModalOpen"
                @close="closeDeleteModal"
                @confirm="confirmDelete"
                class="nova-logs"
                modal-style="window"
                size="7xl"
            >
                <div :class="{
                    'dark': isDark,
                }">
                    <div class="bg-white dark:bg-gray-800 relative">
                        <button type="button" @click="closeStackModal"
                                class="bg-red-100 text-red-600 dark:bg-red-400 dark:text-red-900 rounded-lg p-2 absolute top-4 right-6">
                            <icon class="w-4 h-4" type="x"/>
                        </button>
                        <div class="max-h-[calc(100vh-3rem)] overflow-auto">
                            <div class="p-4 stack-content">
                                {{ stack }}
                            </div>
                        </div>
                    </div>
                </div>
            </Modal>
        </div>
    </div>
</template>

<script>
import ShowLogsRow from '../components/ShowLogsRow';
import dark from '../utils/dark';

export default {
    name: 'ShowLogs',
    components: {
        ShowLogsRow,
    },
    props: ['date', 'level', 'baseUrl'],
    data() {
        return {
            info: null,
            log: null,
            currentLevel: null,
            levels: null,
            entries: null,
            menus: null,
            deleteModalOpen: false,
            deleting: null,
            next: null,
            prev: null,
            isDark: false,

            stackModalOpen: false,
            stack: null,
            colors: Nova.appConfig.viewer,
        };
    },
    computed: {
        downloadUrl() {
            return '/nova-vendor/php-junior/nova-log-viewer/download/';
        },
    },
    mounted() {
        this.getLogByLevel(this.level);
        dark((isDark) => {
            this.isDark = isDark;
            this.colors['empty'] = isDark ? '#3e3e3e' : Nova.appConfig.viewer['empty'];
        });
    },
    methods: {
        visitLogByLevel(level) {
            const newData = {
                ...window.history.state,
                url: newUrl,
            };
            newData.props.level = level;
            const newUrl = `${ this.baseUrl }/list/${ this.date }/${ level }`;
            history.replaceState(newData, '', newUrl);
            this.getLogByLevel(level);
        },
        getLogByLevel(level) {
            this.getLog(`/nova-vendor/php-junior/nova-log-viewer/get/${ this.date }/${ level }`);
        },
        async getLog(url) {
            url =
                url ||
                `/nova-vendor/php-junior/nova-log-viewer/get/${ this.date }/all`;
            const {data} = await Nova.request().get(url);
            this.log = data.log;
            this.levels = data.levels;
            this.currentLevel = data.level;
            this.entries = data.entries.data;
            this.menus = data.menus;
            this.prev = data.entries.prev_page_url;
            this.next = data.entries.next_page_url;
            this.info = data.info;
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

        async confirmDelete() {
            await Nova.request().delete('/nova-vendor/php-junior/nova-log-viewer/delete', {
                data: {
                    date: this.deleting.date,
                }
            });

            this.deleteModalOpen = false;
            this.$inertia.visit(`${ this.baseUrl }/list`);
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

.dark .stack-content {
    color: #df9797;
}
</style>
