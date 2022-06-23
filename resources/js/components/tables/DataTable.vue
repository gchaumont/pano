<template>
    <table class="w-full sm:rounded-lg text-sm text-left text-slate-500 dark:text-slate-400" style="position: relative">
        <thead class="text-xs text-slate-700 uppercase bg-slate-50 dark:bg-slate-700 dark:text-slate-400">
            <tr>
                <pano-field-table-header />
                <template v-for="field in resource.fields">
                    <pano-field-table-header @sortBy="sortBy" class="px-3 py-3" :config="field" v-if="field.type != 'hidden-field'" />
                </template>
            </tr>
        </thead>
        <tbody class="text-slate-500 bg-white dark:bg-slate-800 dark:text-slate-300">
            <tr v-for="model in models" class="group border-b dark:border-slate-700">
                <td class="px-3 py-2 text-slate-400 group-hover:dark:text-white group-hover:text-blue-700 ">
                    <router-link class="hovered" :to="model.link">View</router-link>
                </td>
                <template v-for="field, key in resource.fields">
                    <td class="px-5  py-4 " v-if="field.type != 'hidden-field'">
                        <component :is="field.type" :field="field" :value="model.fields[field.key]" />
                    </td>
                </template>
                <!-- <td v-for="field in resource.fields">{{model[field.key]}}</td> -->
            </tr>
        </tbody>
    </table>
    <div class="w-full my-2 z-0 flex  sticky bottom-0">
        <pano-table-footer :pages="Math.floor(total/50)" :current="state.page" @topage="toPage" />
    </div>
</template>
<script setup>
import { reactive, onMounted, watch, ref } from 'vue'


const state = reactive({
    page: 1,
    sort: null,
    order: null,
})


const emit = defineEmits(['toPage', 'sortBy'])


const props = defineProps({
    resource: {
        type: Object,
        required: true,
    },
    models: {
        type: Array,
        required: true,
    }, 
    total: {
        type: Number, 
        required: true, 
    }
})

function toPage(page) {
    state.page = page;
    emit('toPage', page);
}

function sortBy(sortKey) {
    emit('sortBy', sortKey);
}

onMounted(() => {

})
</script>