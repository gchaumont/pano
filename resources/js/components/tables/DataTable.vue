<template>        
    <table class="w-full sm:rounded-lg text-sm text-left text-slate-500 dark:text-slate-400" style="position: relative">
        <thead class="text-xs text-slate-700 uppercase bg-slate-50 dark:bg-slate-700 dark:text-slate-400">
            <tr>
                <pano-field-table-header />
                <template v-for="field in fields">
                    <pano-field-table-header @sortBy="sortBy" class="px-3 py-3" :config="field" v-if="field.type != 'hidden-field'" />
                </template>
            </tr>
        </thead>
        <tbody class="text-slate-500 bg-white dark:bg-slate-800 dark:text-slate-300">
            <tr v-for="model, index in models" class="group border-b dark:border-slate-700">
                <td class="px-3 py-2 text-slate-400 group-hover:dark:text-white group-hover:text-blue-700 ">
                    <router-link class="hovered" :to="model.link">View</router-link>
                         <div  v-if="models.length>0 && index == models.length-5 " v-intersect="loadMore"  >
                    </div>
                </td>
                <template v-for="field, key in fields">
                    <td class="px-5  py-4 " v-if="field.type != 'hidden-field'">
                        <component :is="field.type" :field="field" :value="model.fields[field.key]" />
                    </td>
                </template>
                <!-- <td v-for="field in resource.fields">{{model[field.key]}}</td> -->
            </tr>
        </tbody>
    </table>
        <div v-if="isLoading" class="flex justify-center items-center h-60">
            <loading-spinner   />
        </div>

    <!-- <div class="w-full my-2 z-0 flex  sticky bottom-0">
        <pano-table-footer :pages="Math.floor(total/50)" :current="props.page" @topage="toPage" />
    </div> -->
</template>
<script setup>
import { reactive, onMounted, watch, ref } from 'vue'


const state = reactive({
    sort: null,
    order: null,
})


const emit = defineEmits(['toPage', 'sortBy'])


const props = defineProps({
    fields: {
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
    },
    page: {
        type: Number,
        required: true,
    },
    isLoading: {
        type: Boolean,
        required: true,
    }
})

function toPage(page) {
    emit('toPage', page);
}

function loadMore(intersect) {

    if (intersect.intersectionRatio == 1 && props.isLoading == false) {
        emit('toPage', props.page + 1)
    }
    // if (intersect.intersectionRatio < 1 && intersect.intersectionRatio > 0) {
    //     console.log('loading more')
    //     emit(props.page + 1)
    // }
}

function sortBy(sortKey) {
    emit('sortBy', sortKey);
}

onMounted(() => {

})
</script>