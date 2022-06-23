<template>
    <span></span>
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
    computed: {
        valueStyle: function () {
            var style = {};
            // if (this.field.alignment !== null) {
            //     style['text-align'] = this.field.alignment;
            // }
            return style
        }
    },
}
</script>