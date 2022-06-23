<template>
    <div class="pr-3">
        <header>
            <h2 class="text-slate-600 dark:text-slate-400 text-3xl pt-2.5 ">{{resource.name}}</h2>
        </header>
        <section>
            <ul class="flex flex-row flex-wrap  gap-3 items-stretch  justify-start my-4">
                <li v-for="metric in resource.metrics" class="flex-auto min-w-[30ch] max-w-[60ch]">
                    <component class="p-4 pb-6 rounded-lg  bg-card h-full" :is="metric.type+'-metric'" :metric="metric" :path="resource.path" :search="state.search" />
                </li>
            </ul>
        </section>
        <section style="position: relative;">
            <model-search class="my-4" ref="searchbar" :resource="resource" @search="handleQuery"/>

            <div class="min-h-screen" >
                <data-table @toPage="toPage" @sortBy="sortBy" :resource="resource" :models="state.models" :total="state.total" />
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
    total: 0,
    page: 1,
    search: '',
})

const searchbar = ref(null)


const props = defineProps({
    resource: {
        type: Object,
        required: true,
    }
})

function loadResource() {
    state.models = []
    fetch(props.resource.path + "?" + new URLSearchParams({ search: state.search, page: state.page, sort: route.query.sort }), { headers: { 'Accept': 'application/json' } })
        .then(response => response.json().then(json => {
            state.models = json.hits
            state.total = json.total
        }))
}

function handleQuery(query) {
    state.search = query;
    toPage(1),  
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
    router.replace({ query: Object.assign({}, route.query, { page: state.page }) , hash :'#'})
}

function sortBy(sortKey) {
    router.replace({ query: Object.assign({}, route.query, { sort: sortKey }) })
}

onMounted(() => {
    if (route.query.page) {
        state.page = Math.max(1, parseInt(route.query.page));
    }
    loadResource()
    watch(
        () => props.resource,
        () => { toPage(1), loadResource() }
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