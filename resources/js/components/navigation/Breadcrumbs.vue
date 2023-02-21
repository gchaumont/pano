<template>
    <nav class="flex mb-1" aria-label="Breadcrumb">
        <ol role="list" class="flex items-center space-x-2 flex-wrap">

            <li v-for="crumb, idx in breadcrumbs" >
                <div class="flex items-center">
                    <ChevronRightIcon v-if="idx > 0" class="h-5 w-5 flex-shrink-0 text-gray-500 mr-4" aria-hidden="true" />
                    <router-link :to="crumb.url" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white whitespace-nowrap" >{{crumb.name}}</router-link>
                </div>
            </li>
        
        </ol>
    </nav>
</template>
<script setup>
import { ChevronRightIcon } from '@heroicons/vue/20/solid'
import { useRoute } from 'vue-router'
import { inject } from 'vue'

const theme = inject('theme');
const route = useRoute();
const props = defineProps({
    breadcrumbs: {
        type: Array, 
        required: true,
    }
})
</script>