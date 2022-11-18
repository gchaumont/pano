<template>
    <div v-if="value?.length">
        <ul  class="min-w-[30ch] border border-gray-200 dark:border-gray-400 rounded-md divide-y divide-gray-200 dark:divide-gray-400 bg-white dark:bg-slate-600">
            <li v-show=" field.max == null || key < field.max || showAll " v-for="item, key in value" class="pl-3 pr-4 py-3 flex  text-sm">
                <ul class="w-full">
                    <li v-for="subField, key in field.fields" v-show="subField.type != 'hidden-field'" class="px-6 py-1 sm:grid sm:grid-cols-3 sm:gap-4">

                        <div style="flex: 0 0 20ch" class="text-gray-500 dark:text-gray-300">{{subField.name}}</div>
                        <component :is="subField.type" :field="subField" :value="item[key]" class="sm:col-span-2" />
                    </li>
                </ul>
            </li>
        </ul>
        <button v-show="showAll == false && field.max !== null && value.length > field.max" @click="showAll = true">Show All</button>
    </div>
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
    data() {
        return {
            showAll: false,
        }
    },
    mounted() {

    }
}
</script>