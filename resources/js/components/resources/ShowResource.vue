<template>
        
    <PageHeading :title="data.model?.title || resource.name" :actions="resource.actions" :breadcrumbs="breadcrumbs" :meta="resource.meta" class="dark:bg-slate-800 p-5"/>
    <p v-if="data.error" class="text-slate-700 dark:text-slate-300 text-xl pt-6 ">{{data.error}}</p>
    <div class="p-5 mt-3" v-if="data.model">
        <section :class="[theme.tableBody, ' shadow overflow-hidden sm:rounded-lg']">

            <header class="px-4 py-5 sm:px-6">
                <h2 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-200">{{data.model.title}}</h2>
                <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">details</p>
            </header>
            <dl class="border-t border-gray-200 dark:border-gray-500">
                <template v-for="field, key in simpleFields">
                    <div class="dark:even:bg-slate-700/50 dark:odd:bg-slate-800 even:bg-slate-100 odd:bg-slate-200 px-4 py-5 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6" v-if="field.type != 'hidden-field'">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{field.name}}</dt>
                        <dd class="mt-1  sm:mt-0 sm:col-span-3 ">
                            <component v-if="data.model.fields" :is="field.type" :field="field" :value="data.model.fields[field.key]" />
                        </dd>
                    </div>
                </template>
            </dl>
        </section>
        <section class="mt-12" v-if="relations">
            <ul class="flex wrap">
                <li @click="data.selectedRelation = relation.key" class="mx-2 text-lg" v-bind:class="{'font-bold' : data.selectedRelation==relation.key}" v-for="relation in relations">{{relation.name}}
                    <br>
                </li>
            </ul>
            <!-- <ListResource v-if="currentRelation" :resource="currentRelation"   :uiPath="uiPath"/> -->
        </section>
    </div>
</template>
<!-- USE SEARCH API -->
<!-- USE DATATABLE -->
<script setup>
import ListResource from './ListResource.vue';
import PageHeading from '@/components/headings/PageHeading.vue'
import {useData} from '@/Pano.js'
import { inject, computed, reactive, watch } from 'vue'

const theme = inject('theme');

const endpoint = useData()

const props = defineProps({
    resource: {
        type: Object,
        required: true,
    },
    record: {
        type: String,
        required: true,
    },
    uiPath: {
        type: String,
        required: true,
    },
})

const data = reactive({
    model: null,
    error: null,
    fields: [],
    selectedRelation: null,
})

console.log(props.resource)
const relations = computed(function() {
    return data.fields.filter((f) => f.type == 'relates-to-many-field');
})
const breadcrumbs = computed(() => [...props.resource.breadcrumbs, {name: props.resource.name, url:props.resource.path}])

const simpleFields = computed(function() {
    return data.fields.filter((f) => f.type != 'relates-to-many-field');
})

// const relatedEndpoint = computed(function() {
//     return props.resource.endpoints.resource.url.replace(':record', props.record).replace(':relation', currentRelation.value.key)
// })
const currentRelation = computed(function() {
    if (data.selectedRelation) {
        var relation = relations.value.find(r => r.key == data.selectedRelation)

        // relation.path = 'relations/'+relation.key
        return relation
    }
})



const loadResource = function() {
        data.model = null

        endpoint.query({endpoint: 'record', params :{record:props.record}, uiPath: props.uiPath})
            .then(r => {
                var json = r.record;

                    data.error = null;
                    data.model = json.model
                    data.fields = json.fields
                    data.selectedRelation = relations.value[0]?.key

                    // console.log(relations.value);


            })

        // fetch(props.resource.endpoints.recordsrecord.url.replace(':record', props.record) + "?" + new URLSearchParams(), { headers: { 'Accept': 'application/json' } })
        //     .then(response => response.json().then(json => {
    
        //     }))
    }

    watch(
        () => props.resource.name + props.record,
        () => loadResource(), { immediate: true }
    )
</script>