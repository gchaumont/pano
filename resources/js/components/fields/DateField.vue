<template>
     <span class="whitespace-nowrap">{{value}}</span>
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