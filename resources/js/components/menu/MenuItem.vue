<template>
    <a v-if="config.path && isAbsolute" :href="config.path" :class="linkClass">
        <component :is="config.icon" v-if="config.icon" :class="iconClass" />
        <span >{{config.name}}</span>
    </a>
    <router-link v-else :to="config.path" :active-class="linkActiveClass" :class="linkClass" v-slot="{isActive}">
        <component :is="config.icon" v-if="config.icon" :class="[isActive && !config.inactive ? 'text-gray-100 group-hover:text-gray-200 dark:text-gray-300 dark:hover:text-gray-300' : 'text-gray-300 dark:group-hover:text-gray-300 group-hover:text-gray-200', iconClass]" />
        <span v-if="!collapsed">{{config.name}}</span>
    </router-link>
</template>
<script setup>
import { computed, inject } from 'vue'

const theme = inject('theme');
const props = defineProps({
    config: {
        type: Object,
        required: true,
    },
    collapsed: {
        type: Boolean,
        required: false,
        default: false
    }, 
 
})
const isAbsolute = computed(() => {
    var regex = new RegExp('^(?:[a-z]+:)?//', 'i')
    return regex.test(props.config.path);
})

const iconClass = "flex-shrink-0 h-6 w-6 mr-2"

const linkClass = " px-2 py-1.5 mb-1 text-gray-200 hover:bg-gray-300/10  border border-transparent  group flex gap-2 items-center  text-sm font-medium rounded-md dark:hover:bg-sky-800/20 dark:text-gray-300 dark:hover:border-sky-700/30 dark:hover:text-sky-100 hover:text-gray-200"

const linkActiveClass =  computed(() => props.config.inactive ? '' : 'dark:!text-sky-100 bg-gray-300/20 hover:!bg-gray-300/20 !text-gray-100 dark:!bg-sky-800/30 dark:border-sky-700/40 dark:hover:!border-sky-700/40 dark:!text-gray-200 dark:hover:!bg-sky-800/30')



</script>