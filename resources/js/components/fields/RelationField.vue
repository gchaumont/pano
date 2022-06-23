<template>
    <router-link class="link" v-if="value" :to="value.link">{{value.title}}</router-link>
</template>
<script>
import Field from './Field';

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