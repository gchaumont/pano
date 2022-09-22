<template>
    <div class="basis-60 shrink-0 min-h-[15rem]">
        <header class="flex justify-between mb-2">
            <h5 class="text-sm text-slate-500 dark:text-slate-400 font-semibold">{{metric.name}}</h5>
            <select class="text-sm" v-if="metric.ranges?.length" v-model="selectedRange">
                <option v-for="range in metric.ranges" :value="range.key">{{range.name}}</option>
            </select>
        </header>
        <!-- <p>{{value?.prefix}}  {{value?.suffix}}</p> -->
        <!-- <div class="w-72"> -->
        <div :id="chartID" class="h-full pt-4 min-w-[50ch]"></div>
        <!-- </div> -->
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

.ct-label.ct-horizontal{
    transform: rotate(-40deg);
    white-space: nowrap;
}

.ct-grids line {
    stroke: grey;
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
            selectedRange: null,
            chartID: 'chartist-' + Math.floor(Math.random() * 10000)
        }
    },
    methods: {
        loadMetric: function() {
            fetch(this.path + "/metrics/" + this.metric.key + "?" + new URLSearchParams({ search: this.search, range: this.selectedRange }), { headers: { 'Accept': 'application/json' } })
                .then(response => response.json().then(json => {
                    this.value = json
                    new Chartist.Line(
                        '#' + this.chartID, { series: [this.value.trend], labels: this.value.labels }, {
                            showPoint: false,
                            chartPadding:0,
                            chartPadding: {left:15, right: 0, top: 10, bottom: 20},
                            // axisX: { showLabel: false }
                        }
                        // {lineSmooth: true,   axisX: {showGrid: true}, axisY: {showGrid: true}}
                    )
                }))
        }
    },
    mounted() {
        this.selectedRange = this.metric.defaultRange;

        this.$watch(
            () => this.selectedRange + this.search,
            () => this.loadMetric(), { immediate: true }
        );
    }
}
</script>