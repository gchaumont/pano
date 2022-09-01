<template>
    <div>
        <header class="mb-2">
            <h5 class="text-sm text-slate-500 dark:text-slate-400 font-semibold">{{metric.name}}</h5>
        </header>
        <div class="flex flex-nowrap justify-evenly">
            <table class="mt-4 h-2 text-sm dark:text-gray-400 whitespace-nowrap">
                <tr v-for="item in value?.partition">
                    <td>
                        <a v-if="value.field" href="" @click.prevent="filter(item)" class="text-slate-600 dark:text-slate-300 font-semibold">{{item.name}} </a>
                        <span v-else class="text-slate-600 dark:text-slate-300 font-semibold">{{item.name}} </span>
                    </td>
                    <td class="pl-4 text-right text-slate-500 dark:text-slate-400"><span>{{value?.prefix}} {{item.value}} {{value?.suffix}} </span></td>
                </tr>
            </table>
            <div :id="chartID"></div>
        </div>
    </div>
</template>
<style type="text/css">
.ct-chart-donut .ct-label {
    /*color: var(--textColor);*/
    @apply  fill-slate-900 dark:fill-slate-100;
}
</style>
<script>
import Chartist from 'chartist';
export default {
    props: {
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
    },
    data() {
        return {
            value: null,
            chartID: 'chartist-' + Math.floor(Math.random() * 10000)
        }
    },
    methods: {
        loadMetric: function() {
            fetch(this.path + "/metrics/" + this.metric.key + "?" + new URLSearchParams({ search: this.search, range: this.selectedRange }), { headers: { 'Accept': 'application/json' } })
                .then(response => response.json().then(json => {
                    this.value = json
                    new Chartist.Pie(
                        '#' + this.chartID, { series: this.value.partition }, { donut: true, donutWidth: 15, labelOffset: 15, chartPadding: 15 }
                    )
                }))
        },
        filter: function (item) {
            this.$emit('search', this.search+ " "+ this.value.field+":\""+item.name+"\"")
        }
    },
    mounted() {
        this.loadMetric()

        this.$watch(
            () =>  this.search,
            () => this.loadMetric(), { immediate: true }
        );
    }
}
</script>