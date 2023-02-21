<template>
    <router-link class="link" v-if="value" :to="value.link">
    {{value.title}}
    <br><small v-if="value.subtitle">{{value.subtitle}}</small>
</router-link>
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
    mounted() {
    }
}
</script>