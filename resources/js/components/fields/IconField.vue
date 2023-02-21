<template>
    <img v-if="value"  :src="value" class="w-[1.5rem] h-[1.5rem]">
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