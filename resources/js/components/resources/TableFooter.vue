<template>
    <div class="flex flex-nowrap ">
        <button class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-slate-500 bg-white dark:bg-slate-700 text-sm font-medium text-gray-500 dark:text-slate-300 hover:bg-slate-100 disabled:hover:bg-white dark:hover:bg-slate-500 disabled:dark:hover:bg-slate-700 disabled:cursor-default"  :disabled="!(current>2)" @click="$emit('topage', 1)">1</button>
        <button class="bg-white dark:bg-slate-700 border-gray-300 dark:border-slate-500 text-gray-500 dark:text-slate-300 hover:bg-slate-100 disabled:hover:bg-white dark:hover:bg-slate-500 disabled:dark:hover:bg-slate-700 disabled:cursor-default relative inline-flex items-center px-4 py-2 border text-sm font-medium"  :disabled="!(current>1)" @click="$emit('topage', current-1)">←</button>
        <button class="bg-white dark:bg-slate-700 border-gray-300 dark:border-slate-500 text-gray-500 dark:text-slate-300 hover:bg-slate-100 disabled:hover:bg-white dark:hover:bg-slate-500 disabled:dark:hover:bg-slate-700 disabled:cursor-default relative inline-flex items-center px-4 py-2 border text-sm font-medium"  disabled>{{current}}</button>
        <button class="bg-white dark:bg-slate-700 border-gray-300 dark:border-slate-500 text-gray-500 dark:text-slate-300 hover:bg-slate-100 disabled:hover:bg-white dark:hover:bg-slate-500 disabled:dark:hover:bg-slate-700 disabled:cursor-default relative inline-flex items-center px-4 py-2 border text-sm font-medium"  :disabled="!(current<pages)" @click="$emit('topage', current+1)">→</button>
        <button class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-slate-500 bg-white dark:bg-slate-700 text-sm font-medium text-gray-500 dark:text-slate-300 hover:bg-slate-100 disabled:hover:bg-white dark:hover:bg-slate-500 disabled:dark:hover:bg-slate-700 disabled:cursor-default"  :disabled="!(current<pages)" @click="$emit('topage', pages)">{{pages || '...'}}</button>
    </div>
</template>
<script>
export default {
    props: {
        pages: {
            required: false,
            type: Number,
            default: null,
        },
        current: {
            required: false,
            type: Number,
            default: 0,
        }
    }
}
</script>