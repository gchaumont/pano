<template>

    <span :class="cssClass"> {{value}}</span>
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
        type: function () {
            return  this.field.map[this.value];
        },
        cssClass: function () {
             switch(this.type) {
                case 'info':
                    return 'bg-blue-100 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-blue-200 dark:text-blue-800'
                case 'success':
                    return 'bg-green-100 text-green-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-green-200 dark:text-green-900'
                case 'warning':
                    return 'bg-yellow-100 text-yellow-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-yellow-200 dark:text-yellow-900'
                case 'danger':
                    return'bg-red-100 text-red-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-red-200 dark:text-red-900'
                default :
                    return 'bg-gray-100 text-gray-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300'
            }
        } 
    },
    mounted() {

    }
}
</script>