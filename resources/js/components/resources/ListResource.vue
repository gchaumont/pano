<template>
    <div class="pr-3" v-if="state.definition">
        <header>
            <h2 class="text-slate-600 dark:text-slate-400 text-3xl pt-2.5 ">{{state.definition.name}}</h2>
        </header>
        <section v-if="props.show_metrics">
            <ul class="flex flex-row flex-wrap  gap-3 items-stretch  justify-start my-4">
                <li v-for="metric in state.definition.metrics" class="flex-auto min-w-[30ch] max-w-[60ch]">
                    <component class="p-4 pb-6 rounded-lg  bg-card h-full" :is="metric.type+'-metric'" :metric="metric" :path="state.definition.path" :search="state.search" @search="handleQuery"/>
                </li>
            </ul>
        </section>
        <section style="position: relative;">
            <header class="flex flex-wrap gap-3 items-center text-slate-700  dark:text-slate-300">
                <model-search class="my-4" ref="searchbar" :path="state.definition.path" @search="handleQuery" :initialSearch="state.search" />
                <p><span v-if="state.total == 10000">></span>
                    {{state.total}} hits</p>
            </header>
            <div class="min-h-screen">
                <data-table @toPage="toPage" @sortBy="sortBy" :fields="state.fields" :models="state.models" :total="state.total" :page="state.page" :isLoading="state.isLoading" />
            </div>
        </section>
    </div>
</template>
<!-- USE SEARCH API -->
<!-- USE DATATABLE -->
<script setup>
import { reactive, onMounted, watch, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()


const state = reactive({
    models: [],
    fields: [],
    definition: null,
    total: 0,
    page: 1,
    search: '',
    isLoading: false,
})

const searchbar = ref(null)


const props = defineProps({
    resource: {
        type: Object,
        required: true,
    },
    show_metrics: {
        type: Boolean,
        required: false,
        default: false
    }
})

function loadResource() {
    state.isLoading = true
    if (state.page == 1) {

        state.models = []
    }
    var path = props.resource.path;
    if (!path.startsWith('/')) {
        path = window.location.pathname + '/' + path;
    }
    fetch(path + "?" + new URLSearchParams({ search: state.search, page: state.page, sort: route.query.sort }), { headers: { 'Accept': 'application/json' } })
        .then(response => response.json().then(json => {
            state.isLoading = false;

            state.definition = json.resource
            state.fields = json.fields
            if (state.page > 1) {
                console.log('Pushing Hits')
                state.models.push(...json.hits)
            } else {
                console.log('Initialising Hits')
                state.models = json.hits
            }
            state.total = json.total
        }))
}


function handleQuery(query) {
    state.search = query;
    state.page = 1;
    router.replace({ query: Object.assign({}, route.query, { search: state.search, page: state.page }) })
    loadResource()
}

function getValue(value, field) {
    var index = field.indexOf('.');
    if (index >= 0) {
        return getValue(value[field.substring(0, index)], field.substring(index + 1))
    }

    if (typeof value === 'object' && value.hasOwnProperty(field)) {
        return value[field]
    }
}

function toPage(page) {
    state.page = page;
    // router.replace({ query: Object.assign({}, route.query, { page: state.page }), hash: '#' })
}

function sortBy(sortKey) {
    router.replace({ query: Object.assign({}, route.query, { sort: sortKey }) })
}

function resetMetrics() {
    state.definition.metrics = [];
}

onMounted(() => {
    state.definition = props.resource
    state.search = route.query.search || '';

    // if (route.query.page) {
    //     state.page = Math.max(1, parseInt(route.query.page));
    // }
    loadResource()
    watch(
        () => props.resource,
        () => { toPage(1), loadResource(), resetMetrics() }
    )
    watch(
        () => state.page,
        () => loadResource()
    )
    watch(
        () => route.query.sort,
        () => { toPage(1), loadResource() }
    )
})
</script>