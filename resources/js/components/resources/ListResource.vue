<template>
    <div v-if="resourceDefinition">
        <h2>{{resourceDefinition.name}}</h2>
        <input type="search" name="">
        <table style="width: 100%">
            <thead>
                <pano-field-table-header :config="field" v-for="field in resourceDefinition.fields"/>
            </thead>
                <tr v-for="model in models">
                    <td v-for="field in resourceDefinition.fields">{{this.getValue(model, field.field)}}</td>
                </tr>
        </table>
    </div>
</template>
<!-- USE SEARCH API -->
<!-- USE DATATABLE -->
<script>
export default {
    data() {
        return {
            models: [],
        }
    },
    props: {
        resource: {
            type: String,
            required: true,
        }
    },
    computed: {
        resourceDefinition: function() {
            return this.$pano.getResource(this.resource);
        }
    },
    methods: {
        loadResource: function() {
            this.models = []
            fetch(this.$pano.root + '/api/resources/' + this.resourceDefinition.key)
                .then(response => response.json().then(json => {

                    this.models = json
                }))
        }, 
        getValue: function (value, field) {
            var index = field.indexOf('.');
            if (index >= 0) {
                // console.log(field.substring(0,index), field.substring(index+1))
                return this.getValue(value[field.substring(0,index)], field.substring(index+1))
            } 
            return value[field]
        }
    },
    mounted() {
        this.$watch(
            () => this.resource,
            () => this.loadResource(), { immediate: true }
        )
    }
}

</script>
