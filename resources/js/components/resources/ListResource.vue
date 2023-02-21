<template>
    <div >
        <PageHeading :title="resource.name" :actions="resource.actions" :breadcrumbs="resource.breadcrumbs" :meta="resource.meta" class="p-2 sm:p-5"/>
        <section v-if="state.definition && props.show_metrics" class="max-w-full overflow-x-scroll overflow-y-hidden">
            <ul class="flex flex-row gap-3 items-stretch justify-start my-4 max-h-[25rem] mx-2 sm:mx-5">
                <li v-for="metric in state.definition.metrics" class="flex-auto ">
                    <component :class="[theme.cardBg, ' rounded-lg  h-full']" :is="metric.type+'-metric'" :metric="metric" :path="state.definition.path" :search="state.search" @search="handleQuery" />
                </li>
            </ul>
        </section>
    </div>
    <section v-if="state.definition" style="position: relative; " class="p-2 sm:p-5 ">
        <header class="flex flex-wrap gap-3 items-center text-slate-700  dark:text-slate-300 ">
            <model-search class="my-4" ref="searchbar" :path="state.definition.path" @search="handleQuery" :initialSearch="state.search" />
            <p><span v-if="state.total == 10000">></span>
                {{(state.total).toLocaleString('de-CH')}} hits</p>
        </header>
        <div class="min-h-screen overflow-x-scroll ">
            <data-table @nextPage="nextPage" @sortBy="sortBy" :fields="state.fields" :models="state.models" :total="state.total" :page="state.page" :isLoading="state.isLoading" />
        </div>
    </section>
</template>
<!-- USE SEARCH API -->
<!-- USE DATATABLE -->
<script setup>
import { reactive, onMounted, watch, ref, inject } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import PageHeading from '@/components/headings/PageHeading.vue'

const route = useRoute()
const router = useRouter()

const theme = inject('theme')

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
        default: true
    },
    endpoint: {
        type: String,
        required: false,
        default: null
    }
})

function loadResource() {
    state.isLoading = true
    if (state.page == 1) {
        state.models = []
    }
    console.log(props.resource)
    fetch((props.endpoint || props.resource.endpoints['index'].url) + "?" + new URLSearchParams({
            search: state.search,
            page: state.page,
            sort: route.query.sort
        }), {
            headers: { 'Accept': 'application/json' },
            endpoint: 'index',
        })
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
    router.replace({
        query: Object.assign({}, route.query, {
            search: state.search,
            page: state.page
        })
    })
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

function nextPage() {
    state.page++;
    // router.replace({ query: Object.assign({}, route.query, { page: state.page }), hash: '#' })
}

function sortBy(sortKey) {
    router.replace({ query: Object.assign({}, route.query, { sort: sortKey }) })
}

function resetMetrics() {
    if (state.definition) {

        state.definition.metrics = [];
    }
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
        () => { toPage(1), loadResource(), resetMetrics(), state.search = '' }
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