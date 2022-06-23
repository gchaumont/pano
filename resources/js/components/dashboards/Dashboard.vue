<template>
    <div class="pr-3">
        <header>
            <h2 class="text-slate-600 dark:text-slate-400 text-3xl pt-2.5 ">{{dashboard.name}}</h2>
        </header>
        <section>
            <ul class="flex flex-row flex-wrap  gap-3 items-stretch  justify-start my-4">
                <li v-for="metric in dashboard.metrics" class="flex-auto min-w-[30ch] max-w-[50ch]">
                    <component class="p-4 pb-6 rounded-lg  bg-card h-full" :is="metric.type+'-metric'" :metric="metric" :path="dashboard.path" :search="state.search" />
                </li>
            </ul>
        </section>
    </div>
</template>
<!-- USE SEARCH API -->
<!-- USE DATATABLE -->
<script setup>
import { reactive, onMounted, watch, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()


const state = reactive({
    model: [], 
    total: null, 
    page: 1, 
    search: '',
})

const searchbar = ref(null)


const props = defineProps({
    dashboard: {
        type: Object,
        required: true,
    }
})



onMounted(() => {

})
</script>