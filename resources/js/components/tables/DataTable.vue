<template>
    <table class="w-full sm:rounded-lg text-sm max-w-full text-left text-zinc-500 dark:text-zinc-400 ">
        <!-- border-separate  border-spacing-0 -->
        <thead class="text-xs text-zinc-700 uppercase bg-zinc-50 dark:bg-zinc-500 dark:text-zinc-300">
            <tr v-if="fields.length">
                <!-- <pano-field-table-header /> -->
                <th scope="col" :class="[theme.tableHeader, 'relative w-16 px-8 sm:w-12 sm:px-6']">
                    <input type="checkbox" :class="['absolute left-6 top-1/2 -mt-2 h-4 w-4 rounded  focus:ring-sky-500 sm:left-4']" :checked="indeterminate || selectedItems.length === models.length" :indeterminate="indeterminate" @change="selectedItems = $event.target.checked ? models.map((p) => p.id) : []" />
                </th>
                <template v-for="field in fields">
                    <pano-field-table-header @sortBy="sortBy" class="px-3 py-3" :config="field" v-if="field.type != 'hidden-field'" />
                </template>
            </tr>
        </thead>
        <tbody :class="theme.tableBody">
            <tr v-for="model, index in models" :class="[theme.tableRow, 'group border-b ', selectedItems.includes(model.id) && theme.tableRowSelected]">
                <!--   <td class="px-3 py-2 text-zinc-400 group-hover:dark:text-white group-hover:text-blue-700 ">
                    <router-link class="hovered" :to="model.link">View</router-link>
                    </div>
                </td> -->
                <td class="relative w-16 px-8 sm:w-12 sm:px-6">
                    <div v-if="selectedItems.includes(model.id)" class="absolute inset-y-0 left-0 w-0.5 bg-sky-600"></div>
                    <input type="checkbox" class="absolute left-6 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500 sm:left-4" :value="model.id" v-model="selectedItems" />
                </td>
                <template v-for="field, key in fields">
                    <td v-if="field.type != 'hidden-field'" class="p-0">
                        <router-link class="block h-full px-5  py-4 " :to="model.link">
                            <component :is="field.type" :field="field" :value="model.fields[field.key]" />
                            <span class="invisible" v-if="models.length>0 && index > models.length-30 " v-intersect="loadMore" />
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
import { reactive, onMounted, watch, ref, inject, computed } from 'vue'

const theme = inject('theme')

const checked = ref(false)
const selectedItems = ref([])
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


const indeterminate = computed(() => selectedItems.value.length > 0 && selectedItems.value.length < props.models.length)
</script>