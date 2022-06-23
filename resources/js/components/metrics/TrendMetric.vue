<template>
    <div class="basis-60 shrink-0">
        <header class="flex justify-between mb-2">
            <h5 class="text-sm text-slate-500 dark:text-slate-400 font-semibold">{{metric.name}}</h5>
            <select class="text-sm" v-if="metric.ranges?.length" v-model="selectedRange">
                <option v-for="range in metric.ranges" :value="range.key">{{range.name}}</option>
            </select>
        </header>
        <!-- <p>{{value?.prefix}}  {{value?.suffix}}</p> -->
        <div :id="chartID" class="chartist-container"></div>
    </div>
</template>
<style type="text/css">
    .chartist-container {
        padding-top:  1rem;
        height:  100%;
    }
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
.ct-grids line  {
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
                        '#' + this.chartID, 
                        { series: [this.value.trend] }, 
                        { showPoint: false, 
                            chartPadding: 0,
                            axisX: {showLabel: false}
                        }
                        // {lineSmooth: true,   axisX: {showGrid: true}, axisY: {showGrid: true}}
                    )
                }))
        }
    },
    mounted() {
        this.selectedRange = this.metric.defaultRange;

        this.$watch(
            () => this.selectedRange,
            () => this.loadMetric(), { immediate: true }
        );
    }
}
</script>