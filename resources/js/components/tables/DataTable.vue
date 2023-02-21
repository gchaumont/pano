<template>
    <table class="w-full sm:rounded-lg text-sm max-w-full text-left text-zinc-500 dark:text-zinc-400 ">
        <!-- border-separate  border-spacing-0 -->
        <thead class="text-xs text-zinc-700 uppercase bg-zinc-50 dark:bg-zinc-500 dark:text-zinc-300">
            <tr>
                <!-- <pano-field-table-header /> -->
                <template v-for="field in fields">
                    <pano-field-table-header @sortBy="sortBy" class="px-3 py-3" :config="field" v-if="field.type != 'hidden-field'" />
                </template>
            </tr>
        </thead>
        <tbody :class="theme.tableBody" >
            <tr v-for="model, index in models" :class="[theme.tableRow, 'group border-b ']">
                <!--   <td class="px-3 py-2 text-zinc-400 group-hover:dark:text-white group-hover:text-blue-700 ">
                    <router-link class="hovered" :to="model.link">View</router-link>
                    </div>
                </td> -->
                <template v-for="field, key in fields">
                    <td v-if="field.type != 'hidden-field'" class="p-0">
                        <router-link class="block h-full px-5  py-4 " :to="model.link">
                            <component :is="field.type" :field="field" :value="model.fields[field.key]" />
                            <span class="invisible"  v-if="models.length>0 && index > models.length-30 " v-intersect="loadMore"  />
                        </router-link>
                    </td>
                </template>
                <!-- <td v-for="field in resource.fields">{{model[field.key]}}</td> -->
            </tr>
        </tbody>
    </table>
    <div v-if="isLoading" class="flex justify-center items-center h-[50vh]">
        <loading-spinner />
    </div>
</template>
<script setup>
import { reactive, onMounted, watch, ref, inject } from 'vue'
const theme = inject('theme')


const state = reactive({
    sort: null,
    order: null,
    emitting: null,
})


const emit = defineEmits(['nextPage', 'sortBy'])


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
    },
})

function loadMore(intersect) {
    if (state.emitting == true) {
        return
    }
    state.emitting = true;
    if (intersect.intersectionRatio == 1 && props.isLoading == false) {
        emit('nextPage')
    }
    setTimeout(() => state.emitting = false, 200);
}

function sortBy(sortKey) {
    emit('sortBy', sortKey);
}

onMounted(() => {

})
</script>