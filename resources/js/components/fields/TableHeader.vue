<template>
    <th :class="[theme.tableHeader, 'sticky top-0 bg-slate-100  first:rounded-tl-md last:rounded-tr-md']">
        <div v-if="config" class="flex items-center">
            <template v-if="config.sortable">
                <p :style="valueStyle" @click="sortBy">{{config.name}}</p>
                <div @click="sortBy" style="display: flex; flex-direction: column; align-items: center; margin-left: .5rem">
                    <ChevronUpIcon v-show="!isActiveAsc" :fill="isActiveDesc ? 'black' : 'grey'" style="height: 16px; width: 16px;" />
                    <ChevronDownIcon v-show="!isActiveDesc" :fill="isActiveAsc ? 'black' : 'grey'" style="height: 16px; width: 16px;" />
                </div>
            </template>
            <template v-else>
                <p :style="valueStyle">{{config.name}}</p>
            </template>
        </div>
    </th>
</template>
<script setup>
import { ChevronDownIcon, ChevronUpIcon } from '@heroicons/vue/24/solid'
import { inject, computed, defineEmits } from 'vue'
import {useRoute} from 'vue-router'
const theme = inject('theme');
const route = useRoute();

const emit = defineEmits('sortBy');

const props = defineProps({
    config: {
        type: Object,
        requried: false
    }
})

const sortBy = function() {
    var sortKey = isActiveAsc ? '-' + props.config.key : props.config.key;

    emit('sortBy', sortKey);

}
const isActiveAsc = computed(function() {
    return route.query.sort == props.config.key
})
const isActiveDesc = computed(function() {
    return route.query.sort == '-' + props.config.key
})
const valueStyle = computed(function() {
    var style = {
        'font-weight': 500,
        'white-space': 'nowrap',
        'color': 'var(--interfaceTextColor)'
    };

    return style
})
</script>