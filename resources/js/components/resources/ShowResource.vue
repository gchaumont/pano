<template>
    <h1 class="text-slate-600 dark:text-slate-400 text-3xl pt-2.5 ">{{resource.name}}</h1>
    <div class="p-3 pl-0" v-if="model">
        <section  class="bg-white dark:bg-slate-700 shadow overflow-hidden sm:rounded-lg">
            <header class="px-4 py-5 sm:px-6">
                <h2 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-200">{{model.title}}</h2>
                <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">details</p>
            </header>
            <dl class="border-t border-gray-200 dark:border-gray-500">
                <template v-for="field, key in resource.fields">
                    <div class="dark:even:bg-slate-700 dark:odd:bg-slate-800 even:bg-slate-50 odd:bg-white px-4 py-5 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6" v-if="field.type != 'hidden-field'">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{field.name}}</dt>
                        <dd class="mt-1  sm:mt-0 sm:col-span-3 ">
                            <component  v-if="model.fields" :is="field.type" :field="field" :value="model.fields[field.key]" />
                        </dd>
                    </div>
                </template>
            </dl>
        </section>

        <section class="mt-12" v-if="relations">

            <ul class="flex wrap">

                <li @click="selectedRelation = relation.key" class="mx-2 text-lg" v-bind:class="{'font-bold' : selectedRelation==relation.key}" v-for="relation in relations">{{relation.name}}</li>
            </ul>

            {{relations}}
            <!-- <data-table :resource="relation" :models="[]" /> -->
            
        </section>


    </div>
</template>
<!-- USE SEARCH API -->
<!-- USE DATATABLE -->
<script>
export default {
    data() {
        return {
            model: null,
            selectedRelation: null,
        }
    },
    props: {
        resource: {
            type: Object,
            required: true,
        },
        object: {
            type: String,
            required: true,
        },

    },
    methods: {
        loadResource: function() {
            this.model = []
            fetch(this.resource.path + '/' + this.object + "?" + new URLSearchParams(), { headers: { 'Accept': 'application/json' } })
                .then(response => response.json().then(json => {
                    this.model = json
                    this.selectedRelation = this.relations[0]?.key
                }))
        },
    },
    computed: {
        relations: function () {
            return this.resource.fields.filter((f) => f.type == 'has-many-field');
        }
    },
    mounted() {
        this.$watch(
            () => this.resource.name + this.object,
            () => this.loadResource(), { immediate: true }
        );
    }
}
</script>