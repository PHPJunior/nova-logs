<template>
    <div class="nova-logs">
        <heading class="mb-6">Nova Log Viewer</heading>

        <div class="flex mb-4" :class="{
            'dark': isDark,
        }">
            <div class="w-1/3">
                <Doughnut
                    v-if="loaded"
                    :chart-data="dataCollection"
                    :options="{responsive: true, maintainAspectRatio: true}"
                    :height="400"/>
            </div>
            <div>
                <div class="flex flex-wrap mb-4">
                    <div class="w-1/3 p-4" v-for="(percent, key) in percents" :key="key">
                        <div class="max-w-sm rounded overflow-hidden shadow-lg dark:shadow-gray-600"
                             :style="percent.count !== 0 ? 'color: #FFF;background-color :' + percent.backgroundColor : '' ">
                            <div class="px-6 py-4">
                                <div class="font-bold text-xl mb-2">{{ percent.name }}</div>
                                <p class="text-grey-darker text-base">
                                    {{ percent.count }} entries - {{ percent.percent }}%
                                </p>
                                <div class="progress mt-3">
                                    <div class="bar" :style="{ width: percent.percent + '%' }"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Doughnut } from 'vue-chartjs';
import { ArcElement, Chart as ChartJS, Legend, Tooltip } from 'chart.js'
import dark from '../utils/dark';

ChartJS.register(Tooltip, Legend, ArcElement)

export default {
    components: {
        Doughnut,
    },
    data() {
        return {
            loaded: false,
            dataCollection: {},
            percents: {},
            isDark: false,
        }
    },
    async mounted() {
        this.loaded = false;
        dark((isDark) => this.isDark = isDark);

        try {
            const {data} = await Nova.request().get('/nova-vendor/php-junior/nova-log-viewer/get_chart_data');
            this.dataCollection = data.chartData;
            this.percents = data.percents;
            console.log(data);

            this.loaded = true
        } catch (e) {
            console.error(e)
        }
    },
}
</script>

<style>
.progress {
    width: 100%;
    background-color: #000;
}

.bar {
    height: 5px;
    background-color: #FFF;
}
</style>
