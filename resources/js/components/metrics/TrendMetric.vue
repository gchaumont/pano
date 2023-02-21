<template>
    <div class="basis-60 shrink-0 min-h-[15rem] p-4 pb-2">
        <header class="flex items-center  justify-between mb-2">
            <h5 class="text-sm text-slate-500 dark:text-slate-200 font-semibold">{{metric.name}}</h5>
            <select class="block rounded-md text-slate-600 dark:text-slate-200 border-gray-400 dark:border-gray-500 py-1 pl-3 pr-10 text-base focus:border-sky-500 focus:outline-none focus:ring-sky-500 sm:text-sm" v-if="metric.ranges?.length" v-model="selectedRange">
                <option v-for="range in metric.ranges" :value="range.key">{{range.name}}</option>
            </select>
        </header>
        <!-- <p>{{value?.prefix}}  {{value?.suffix}}</p> -->
        <div class="h-48 pt-4  min-w-[45ch]">
            <canvas :id="chartID" />
        </div>

    </div>
</template>
<style type="text/css">
.ct-series-a .ct-line {
    /* Set the colour of this series line */
    stroke: orange;
    /* Control the thikness of your lines */
    stroke-width: 2px;
    /* Create a dashed line with a pattern */
    /*stroke-dasharray: 10px 20px;*/
}

.ct-label {
    color: var(--textColor);
}

.ct-label.ct-horizontal {
    transform: rotate(-40deg);
    white-space: nowrap;
}

.ct-grids line {
    stroke: grey;
}
</style>
<script setup>
import { onMounted, watch, reactive, ref, inject, computed } from 'vue'
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
const theme = inject('theme');
const value = ref(null);
const selectedRange = ref(null);
const chartID = ref('chartist-' + Math.floor(Math.random() * 10000));

var chart = null;

const loadMetric = () => {
    fetch(props.metric.url + "?" + new URLSearchParams({ search: props.search, range: selectedRange.value }), { headers: { 'Accept': 'application/json' } })
        .then(response => response.json().then(json => {
            value.value = json

            chart.data = {
                labels: json.labels,
                datasets: [
                    {
                        data: json.trend, 
                        borderColor: theme.accentColor,
                        backgroundColor: theme.accentColorTransparent,
                        fill: true,
                        borderWidth: 2,
                        showLine: true,
                        tension: 0.2,
                        pointRadius: 0,
                        pointHitRadius: 30, 
                    }
                ]
            };
            chart.update();

        }))
}

onMounted(() => {
    selectedRange.value = props.metric.defaultRange;
    watch(() => selectedRange.value + props.search, () => loadMetric(), { immediate: true });
    const ctx = document.getElementById(chartID.value);
    // hide chart grid

        chart = new Chart(ctx, {
        type: 'line',
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                    // position: 'left',
                },
                gridLines: {
                    display: false,
                },
            },
            scales: {
                y: { grid: {display: false} },
                x: {
                    grid: {display: false},
                    // beginAtZero: true,
                    ticks: {
                        display: false,
                    },
                }
            }

        }
    });
});


</script>