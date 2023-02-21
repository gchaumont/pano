<template>

    <div>   
        <template  v-for="field, key in field.fields"> 
            
            <component v-if="field.type != 'hidden-field'" :is="field.type" :field="field" :value="value[field.key]"/>
        </template>
    </div>

</template>
<script>
import Field from './Field.vue';

export default {
    extends: Field,
    props: {
        field: {
            type: Object,
            required: true
        }, 
        value: {
            required: false
        }
    },
    computed: {
        valueStyle: function () {
            var style = {};
            // if (this.field.alignment !== null) {
            //     style['text-align'] = this.field.alignment;
            // }
            return style
        }
    },
    mounted() {

    }
}
</script>