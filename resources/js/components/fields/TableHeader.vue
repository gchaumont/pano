<template>
    <th class=" sticky top-0 bg-slate-100 dark:bg-slate-700 first:rounded-tl-md last:rounded-tr-md" >
        <div v-if="config" class="flex items-center dark:text-slate-200">
            <template v-if="config.sortable">
                <p :style="valueStyle" @click="sortBy">{{config.name}}</p>
                <div @click="sortBy" style="display: flex; flex-direction: column; align-items: center; margin-left: .5rem">
          
                    <ChevronUpIcon v-show="!isActiveAsc"  :fill="isActiveDesc ? 'black' : 'grey'"  style="height: 16px; width: 16px;" />
                    <ChevronDownIcon v-show="!isActiveDesc" :fill="isActiveAsc ? 'black' : 'grey'" style="height: 16px; width: 16px;"/>
                </div>
            </template>
            <template v-else>
                <p :style="valueStyle">{{config.name}}</p>
            </template>
        </div>
    </th>
</template>
<script>
import { ChevronDownIcon, ChevronUpIcon } from '@heroicons/vue/24/solid'

export default {
    components :{ ChevronDownIcon, ChevronUpIcon},
    props: {
        config: {
            type: Object,
            requried: false
        }
    },
    methods: {
        sortBy: function() {
        var sortKey = this.isActiveAsc ? '-' + this.config.key : this.config.key;
            
            this.$emit('sortBy', sortKey);

        }
    },
    computed: {
        isActiveAsc: function() {
            return this.$route.query.sort == this.config.key
        },
        isActiveDesc: function() {
            return this.$route.query.sort == '-' + this.config.key
        },
        valueStyle: function() {
            var style = {
                'font-weight': 500,
                'white-space': 'nowrap',
                'color': 'var(--interfaceTextColor)'
            };
            
            return style
        }
    },
    mounted() {}
}
</script>