<template>
 <span :class="[colorBg, 'h-4 w-4 rounded-full flex items-center justify-center']" aria-hidden="true">
      <span :class="[color, 'h-2 w-2 rounded-full']" />
    </span>
</template>
<script setup>
import {computed } from 'vue'
    
const props = defineProps({
    status: {
        type: String,
        required: true,
    },
    simple: {
        type: Boolean, 
        required: false, 
        default: true, 
    }
    
})

const colors = {
    off: 'bg-gray-400',
    info: 'bg-blue-400',
    success: 'bg-green-400',
    danger: 'bg-red-400',
    warning: 'bg-yellow-400',
}

const colorsBg = {
    off: 'bg-gray-500/30 dark:bg-gray-800/30',
    info: 'bg-blue-500/30 dark:bg-blue-800/30',
    success: 'bg-green-500/30 dark:bg-green-800/30',
    danger: 'bg-red-500/30 dark:bg-red-800/30',
    warning: 'bg-yellow-500/30 dark:bg-yellow-800/30',
}


const color = computed(() =>  colors[props.status] || colors.info)
const colorBg = computed(() =>  props.simple  ? null : (colorsBg[props.status] || colorsBg.info))


</script>