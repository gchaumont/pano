<template>
    <div class="p-5" >
        <PageHeading :title="dashboard.name" :breadcrumbs="dashboard.breadcrumbs"/>

        <section>
            <ul class="flex flex-row flex-wrap   items-stretch  justify-start  gap-3 my-4"> 

                <li v-for="metric in dashboard.metrics" class="flex-auto min-w-[30ch] max-w-[50ch]">
                    <component :class="[theme.cardBg, 'p-4 pb-6 rounded-lg  h-full']" :is="metric.type+'-metric'" :metric="metric" :path="dashboard.path" :search="state.search" />
                </li>
            </ul>
        </section>
    </div>
</template>
<!-- USE SEARCH API -->
<!-- USE DATATABLE -->
<script setup>
import { inject, reactive, onMounted, watch, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import PageHeading from '@/components/headings/PageHeading.vue'

const theme = inject('theme')
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



    console.log('adadsa')
onMounted(() => {
})
</script>