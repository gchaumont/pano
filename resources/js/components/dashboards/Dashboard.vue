<template>
    <div class="p-5" >
        <PageHeading :title="dashboard.name" :breadcrumbs="dashboard.breadcrumbs"/>

        <section>
            <ul class="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-3 flex-row flex-wrap   items-stretch  justify-start  gap-3 my-4"> 
                <li v-for="metric in dashboard.metrics" class="flex-auto "> 
                    <!-- min-w-[30ch] max-w-[50ch] -->
                    <component :class="[theme.cardBg, 'p-4 pb-6 rounded-lg  h-full']" :is="metric.component" :metric="metric.props" :path="dashboard.path" :uiPath="metric.uiPath" :key="metric.uiPath" />
                </li>
            </ul>
        </section>
    </div>
</template>
<!-- USE SEARCH API -->
<!-- USE DATATABLE -->
<script setup>
import { inject } from 'vue'
import PageHeading from '@/components/headings/PageHeading.vue'

const theme = inject('theme')

const props = defineProps({
    dashboard: {
        type: Object,
        required: true,
    }
})


</script>