<template>
    <div class="w-64 ">
        <div class="py-4 px-3 ">
            <div v-if="parent">
                <router-link :to="parent.path" class="flex items-center"><ArrowLeftIcon class="h-4 w-4 mr-2"/> {{parent.name}}</router-link>
            </div>
            <ul class="space-y-2">
                <li v-for="menu in config">
                    <component :is="'pano-menu-'+menu.type" :config="menu" />
                </li>
            </ul>
        </div>
    </div>
</template>
<script setup>
    import {ArrowLeftIcon} from '@heroicons/vue/solid';
const props = defineProps({
    components: {ArrowLeftIcon},
    app: {
        type: Object,
        required: true,
    },
    config: {
        type: Object,
        required: true,
    },
    parent: {
        type: Object,
        required: false,
    }
})
</script>