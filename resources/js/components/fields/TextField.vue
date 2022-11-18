<template>
    <p v-if="!Array.isArray(value)" :class="fieldFormat + textAlign"> {{value}}</p>
    <ul v-else>    
        <li v-for="val in value"  :class="fieldFormat + textAlign">{{val}}</li>
    </ul>
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
        fieldFormat: function () {
            switch(this.field.format) {
                case 'heading':
                    return 'text-base text-slate-800 dark:text-slate-200'
                case 'subtitle':
                       return 'font-bold dark:text-slate-400'
                case 'small':
                    return 'text-sm text-slate-400 dark:text-slate-500'
                case 'base':
                default:
                    return ''
            }
        },
        textAlign: function () {
            switch(this.field.align) {
                case 'center':
                    return ' text-center'
                case 'right':
                    return ' text-right'
                case 'justified':
                    return ' text-justified'
                case 'left':
                default:
                    return ' text-left'
            }
        },
 
    },
    mounted() {
    }
}
</script>