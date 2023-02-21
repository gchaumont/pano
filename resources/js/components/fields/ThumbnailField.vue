<template>
    <img v-if="value" :src="value" class="min-w-[2.5rem] min-h-[2.5rem]  max-w-[8rem] max-h-[8rem] object-cover ">
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
}
</script>