<template>
    <router-view v-slot="{ Component }">
        <keep-alive>
            <component :is="Component" />
        </keep-alive>
    </router-view>
    <global-search v-if="app.isRoot" :items="getAppItems(props.app)" />
</template>
<script setup>
import { computed, inject, provide } from 'vue'
import { getRootApp, setCurrentApp, getCurrentApp } from '@/Pano.js'

const props = defineProps({
    app: {
        type: Object,
        required: true,
    },
})

if (props.app.isRoot) {
    console.log(props.app.name + ' is root app')
    provide('rootApp', props.app)
    provide('applicationRootMenu', props.app.menu)
} 
provide('parentConfig', props.app)

const getAppItems = function(app) {
    return [app, ...app.dashboards, ...app.resources, ...app.apps.map(app => getAppItems(app)).flat()]
}
</script>