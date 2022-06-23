<template>
    <div >
        <header class="flex justify-between mb-2">
            <h5 class=" text-sm text-slate-500 dark:text-slate-400 font-semibold">{{metric.name}}</h5>
            <select class="text-sm" v-if="metric.ranges?.length" v-model="selectedRange">
                <option v-for="range in metric.ranges" :value="range.key">{{range.name}}</option>
            </select>
        </header>
        <p class="text-xl mt-4">{{value?.prefix}} {{value?.result}} {{value?.suffix}} <span v-if="value?.previous != null">({{value.previous}})</span></p>
    </div>
</template>
<script>
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
            selectedRange: null
        }
    },
    computed: {
        searchParams: function () {
            var params = {};
            if (this.search) {
                params.search = this.search;
            }
            if (this.selectedRange) {
                params.range = this.selectedRange
            }
            return params;
        }
    },
    methods: {
        loadMetric: function() {
            fetch(this.path + "/metrics/" + this.metric.key + "?" + new URLSearchParams(this.searchParams), { headers: { 'Accept': 'application/json' } })
                .then(response => response.json().then(json => {
                    this.value = json
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