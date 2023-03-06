<template>
    <div>
        <PageHeading :title="resource.name" :actions="resource.actions" :breadcrumbs="resource.breadcrumbs" :meta="resource.meta" class="p-2 sm:p-5" />
        <section v-if="state.definition && props.show_metrics" class="max-w-full overflow-x-scroll overflow-y-hidden">
            <ul class="flex flex-row gap-3 items-stretch justify-start my-4 max-h-[25rem] mx-2 sm:mx-5">
                <li v-for="metric in state.definition.metrics" class="basis-[30rem]">
                    <component :class="[theme.cardBg, ' rounded-lg  h-full']" :is="metric.component" :metric="metric.props" :path="state.definition.path" :params="getQueryParams" @filter="handleFilter" :uiPath="metric.uiPath" :key="metric.uiPath"/>
                    
                </li>
            </ul>
        </section>
    </div>
    <section v-if="state.definition"  class="p-2 sm:p-5 ">
        <header class="flex flex-wrap gap-3 items-center text-slate-700  dark:text-slate-300 ">
            <model-search class="my-4" ref="searchbar" :path="state.definition.path" @search="handleQuery" :initialSearch="state.search" />
            <p class="mr-5 min-w-[10ch]">
                <span v-if="state.total == 10000">></span>
                {{(state.total).toLocaleString(locale)}} hits
            </p>
            <!-- <ul class="flex gap-2">
                    <li v-for="field in state.filterOptions">{{field.name}}</li>
                </ul> -->
            <PopoverGroup class=" sm:flex flex-wrap sm:items-baseline sm:space-x-8">
                <Popover as="div" v-for="(section, sectionIdx) in state.filterOptions" :key="section.name" :id="`desktop-menu-${sectionIdx}`" class="relative inline-block text-left">
                    <div>
                        <PopoverButton class="group inline-flex items-center justify-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-300 ">
                            <span>{{ section.name }}</span>
                            <span v-if="state.filters[section.key]?.length" class="ml-1.5 rounded bg-gray-200 dark:bg-gray-700 py-0.5 px-1.5 text-xs font-semibold tabular-nums text-gray-700 dark:text-gray-300 ">{{state.filters[section.key].length}}</span>
                            <ChevronDownIcon class="-mr-1 ml-1 h-5 w-5 flex-shrink-0 text-gray-400 group-hover:dark:text-gray-300 group-hover:text-gray-500" aria-hidden="true" />
                        </PopoverButton>
                    </div>
                    <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100 " leave-to-class="transform opacity-0 scale-95">
                        <PopoverPanel class="absolute right-0 z-10 mt-2 origin-top-right rounded-md bg-white dark:bg-slate-600 p-4 shadow-2xl ring-1 ring-black ring-opacity-5 focus:outline-none">
                            <form class="space-y-4">
                                <div v-for="(option, optionIdx) in section.options" :key="option.value" class="flex items-center">
                                    <input :id="`filter-${section.id}-${optionIdx}`" :name="`${section.id}[]`" :value="option.value" type="checkbox" v-model="state.filters[section.key]" class="h-4 w-4 rounded border-gray-300 text-sky-600 dark:text-sky-500 focus:ring-sky-500" />
                                    <label :for="`filter-${section.id}-${optionIdx}`" class="ml-3 whitespace-nowrap pr-6 text-sm font-medium text-gray-900 dark:text-slate-200">{{ option.label }}</label>
                                </div>
                            </form>
                        </PopoverPanel>
                    </transition>
                </Popover>
            </PopoverGroup>
        </header>
        <div class="min-h-screen overflow-x-scroll ">
            <data-table @nextPage="nextPage" @sortBy="sortBy" :fields="state.fields" :models="state.models" :total="state.total" :page="state.page" :isLoading="state.isLoading" :key="resource.path"/>
        </div>
    </section>
</template>
<!-- USE SEARCH API -->
<!-- USE DATATABLE -->
<script setup>
import { reactive, onMounted, watch, ref, inject, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useData } from '@/Pano.js'
import {
  Dialog,
  DialogPanel,
  Disclosure,
  DisclosureButton,
  DisclosurePanel,
  Menu,
  MenuButton,
  MenuItem,
  MenuItems,
  Popover,
  PopoverButton,
  PopoverGroup,
  PopoverPanel,
  TransitionChild,
  TransitionRoot,
} from '@headlessui/vue'



import PageHeading from '@/components/headings/PageHeading.vue'

const route = useRoute()
const router = useRouter()

const theme = inject('theme')
const locale = inject('locale')

const state = reactive({
    models: [],
    fields: [],
    filterOptions: [],
    filters: {},
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
    uiPath: {
        type: String,
        required: true,
    },
    show_metrics: {
        type: Boolean,
        required: false,
        default: true
    },
})

const endpoint = useData()

const getQueryParams = computed(()  => {
    var params =   {
        search: state.search,
    }
        Object.keys(state.filters).forEach(key => state.filters[key].length && (params[key] =  state.filters[key]))

        return params
})


const resourceParams = computed(()  => {
   var params =   {
            search: state.search,
            page: state.page,
            sort: route.query.sort, 
        }
    Object.keys(state.filters).forEach(key => state.filters[key].length && (params[key] =  state.filters[key]))


   return params
})


function loadResource() {
    state.definition = props.resource
    state.isLoading = true
    if (state.page == 1) {
        state.models = []
    }


    endpoint.query({endpoint: 'resource', params: resourceParams, uiPath: props.uiPath})
        .then(r => {
            var json = r.resource;
            state.isLoading = false;
            // state.definition = json.resource
            state.fields = json.fields
            state.filterOptions = json.filterOptions
            state.total = json.total

            json.filterOptions.forEach(section => {
                if (!state.filters.hasOwnProperty(section.key)) {
                    state.filters[section.key] = []
                }
            })

            if (state.page > 1) {
                state.models.push(...json.hits)
            } else {
                state.models = json.hits
            }
        })
  
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

function handleFilter({key, value}) {
    console.log(key, value)
    state.filters[key] = [value]
    state.page = 1;
    // router.replace({
    //     query: Object.assign({}, route.query, {
    //         search: state.search,
    //         page: state.page
    //     })
    // })
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

        // state.definition.metrics = [];
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
        () => { toPage(1), loadResource(), resetMetrics(), state.search = '', state.models= [] }
    )
    watch(
        () => state.page,
        () => loadResource()
    )
        watch(
        () => state.filters,
        () => [toPage(1),loadResource()], 
        {deep: true}
    )
    watch(
        () => route.query.sort,
        () => { toPage(1), loadResource() }
    )
})
</script>