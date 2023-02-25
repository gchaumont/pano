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
                    <td :class="[ 'pl-4 text-right text-gray-400 font-medium ']"><span>{{chartData?.prefix}} {{new Intl.NumberFormat(locale).format(item.value)}} {{chartData?.suffix}} </span></td>
                </tr>
            </table>
            <div class="relative h-40 w-40">
                <canvas :id="chartID" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, watch, reactive, ref, inject } from 'vue'
import { useRoute } from 'vue-router'
import { useData } from '@/Pano.js'

const theme = inject('theme');
const locale = inject('locale');

const props = defineProps({
    uiPath: {
        type: String,
        required: true,
    },
    metric: {
        type: Object,
        required: true,
    },
    params: {
        type: Object,
        required: false
    }
})
const endpoint = useData();
const emit = defineEmits(['filter']);
const chartID = 'chartist-' + Math.floor(Math.random() * 10000);
const chartData = ref({});
var chart = null;

onMounted(() => {
    const ctx = document.getElementById(chartID);

    import('chart.js')
        .then(({ Chart, DoughnutController, ArcElement, Tooltip }) => {
            Chart.register(DoughnutController, ArcElement, Tooltip)
            chart = new Chart(ctx, {
                type: 'doughnut',
                options: {
                    animation: {
                        animateRotate: true,
                        animateScale: false
                    },
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



            })

        })
        .then(() => loadMetric());
    watch(
        () => props.params,
        (newP, oldP) => JSON.stringify(newP) !== JSON.stringify(oldP) && loadMetric()
    );
    watch(
        () => props.uiPath,
        (newP, oldP) => loadMetric()
    );
})


const loadMetric = function() {
    endpoint.query({ endpoint: 'value', params: props.params, uiPath: props.uiPath })
        .then(response => {
            response = response.value;

            chart.data = {
                labels: response.partition.map(p => p.name),
                datasets: [{
                    data: response.partition.map(p => p.value),
                    borderColor: theme.accentColor,
                    backgroundColor: theme.accentColorTransparent
                }]
            };
        
            chart.update();

            chartData.value = response


        })
}
const filter = function(item) {
    console.log(props.metric, item)
    emit('filter', { key: chartData.value.field, value: item.id })
    // emit('search', (props.search + " " + chartData.value.field + ":\"" + (item.id || item.name) + "\"").trim())
}
</script>