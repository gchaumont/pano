<template>
    <div class="min-h-[15rem] min-w-[35ch] p-4">
        <header class="flex justify-between items-center gap-10 mb-2">
            <h5 class="text-sm text-slate-500 dark:text-slate-200 font-semibold">{{metric.name}}</h5>
            <select class="block rounded-md text-slate-600 dark:text-slate-200 border-gray-400 dark:border-gray-500 py-1 pl-3 pr-10 text-base focus:border-sky-500 focus:outline-none focus:ring-sky-500 sm:text-sm" v-if="metric.ranges?.length" v-model="selectedRange">
                <option v-for="range in metric.ranges" :value="range.key">{{range.name}}</option>
            </select>
        </header>
        <p :class="['text-xl mt-4']"><span>{{value?.prefix}} {{value?.result}} {{value?.suffix}}</span> <span v-if="value?.previous != null" class="text-sm dark:text-slate-400">({{value.previous}})</span></p>
    </div>
</template>
<script setup>
import { onMounted, watch, reactive, ref, inject, computed } from 'vue'
import { useData } from '@/Pano.js'


const theme = inject('theme');
const props = defineProps({
    metric: {
        type: Object,
        required: true,
    },
    params: {
        type: Object,
        required: false, 
        default: () => ({})
    },
    uiPath: {
        type: String,
        required: true
    }
})

const endpoint = useData();
const value = ref(null);
const selectedRange = ref(null);

const loadMetric = () => {
    var params = ref(props.params);

    if (selectedRange.value) {
        params.value['range'] = selectedRange.value;
    }

    endpoint.query({endpoint: 'value', params, uiPath: props.uiPath})
        .then(response => {
            value.value = response.value;

        })
}

onMounted(() => {
    selectedRange.value = props.metric.defaultRange;
    loadMetric()
    watch(() => selectedRange.value, () => loadMetric())
    watch(() => props.params, (oldP, newP) => oldP !== newP && loadMetric())
});
</script>