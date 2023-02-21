<template>
    <div class="min-h-[15rem] p-4">
        <header class="mb-2">
            <h5 class="text-sm text-slate-500 dark:text-slate-200 font-semibold">{{metric.name}}</h5>
        </header>
        <div class="flex flex-nowrap gap-9 justify-evenly">
            <table class="mt-4 h-2 min-w-[20ch] text-sm dark:text-gray-400 whitespace-nowrap">
                <tr v-for="item in chartData.partition">
                    <td>
                        <a v-if="chartData.field" href="" @click.prevent="filter(item)" class="text-slate-600 dark:text-slate-300 font-semibold">{{item.name}} </a>
                        <span v-else class="text-slate-600 dark:text-slate-300 font-semibold">{{item.name}} </span>
                    </td>
                    <td :class="[ 'pl-4 text-right ']"><span>{{chartData?.prefix}} {{item.value}} {{chartData?.suffix}} </span></td>
                </tr>
            </table>
            <div class="relative h-40 w-40">
                <canvas :id="chartID" />
            </div>
        </div>
    </div>
</template>
<style type="text/css">
.ct-chart-donut .ct-label {
    /*color: var(--textColor);*/
    @apply fill-slate-900 fill-slate-100;
}
</style>
<script setup>
    import {onMounted, watch, reactive, ref, inject} from 'vue'

    const theme = inject('theme');

import Chart from 'chart.js/auto'


const props = defineProps({
    path: {
        type: String,
        required: true,
    },
    metric: {
        type: Object,
        required: true,
    },
    search: {
        type: String,
        required: false
    }
})
const emit = defineEmits(['search']);
const chartID = 'chartist-' + Math.floor(Math.random() * 10000);
const chartData = ref({});
var chart = null;

onMounted(() => {
    const ctx = document.getElementById(chartID);

    chart = new Chart(ctx, {
        type: 'doughnut',
        options: {
            plugins: {

            legend: {
                display: false,
                // position: 'left',
            },
            },
            // scales: {
            //     y: {
            //         beginAtZero: true
            //     }
            // }
        }
    });
})


const loadMetric = function() {

    // props.path + "/metrics/" + props.metric.key +
    fetch(props.metric.url+ "?" + new URLSearchParams({ search: props.search }), { headers: { 'Accept': 'application/json' } })
        .then(response => response.json().then(json => {
            chart.data = {
                labels: json.partition.map(p => p.name),
                datasets: [
                    {
                        data: json.partition.map(p =>p.value),
                        borderColor: theme.accentColor
                    }
                ]
            };
            chart.update();

            chartData.value = json
            // new Chartist.Pie(
            //     '#' + this.chartID, { series: this.value.partition }, { donut: true, donutWidth: 15, labelOffset: 15, chartPadding: 15 }
            // )
        }))
}
const filter = function(item) {
    emit('search', (props.search + " " + chartData.value.field + ":\"" + (item.id || item.name) + "\"").trim())
}


watch(
    () => props.search,
    () => loadMetric(), { immediate: true }
);
</script>