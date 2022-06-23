<template>
    <router-link active-class="bg-slate-300  !text-slate-900 dark:bg-slate-800 dark:!text-slate-300"  class="flex items-center p-2 text-base font-normal text-slate-700 my-0.5 hover:text-slate-900 rounded-lg dark:text-slate-400 hover:bg-slate-300  dark:hover:bg-slate-800 dark:hover:text-slate-300"  v-if="config.path" :to="config.path">
        <component :is="config.icon" v-if="config.icon" class="h-5 w-5" />
        <span class="ml-3">{{config.name}}</span>
    </router-link>
</template>
<script>
export default {
    props: {
        config: {
            type: Object,
            required: true,
        }
    },
    mounted() {

    }

}
</script>