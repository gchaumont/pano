<template>
    <div id="mastgrid" :class="[theme.bg, 'block sm:grid']">
        <nav id="sidebar" class="flex">
            <pano-menu v-if="!app.isRoot" :header="root" :menu="root.menu" :class="theme.rootMenu" :collapsed="!app.isRoot" />
            <pano-menu :header="app" :menu="app.menu" :class="theme.menu" />
        </nav>
        <!-- <pano-navbar id="mastnav" :app="app" /> -->
        <main class="flex-1">
            <router-view v-slot="{ Component }">
                <keep-alive>
                    <component :is="Component" />
                </keep-alive>
            </router-view>
        </main>
        <!-- <global-search :app="root" /> -->
        <global-search :items="getAppItems(root)" />
    </div>
</template>
<style type="text/scss" lang="scss">
    #mastgrid {
        // display: grid;
        align-items: start;
        max-width: 100vw;
        min-height: 100vh;
        grid-template-columns: minmax(30ch, 30ch) 1fr 1fr;
        // grid-template-rows: 3.5rem 1fr;
        justify-items: stretch;
        align-items: stretch;
        grid-template-areas:
            // "sidebar header header"
            "sidebar main main";
    ;

    #mastnav {
        grid-area: header;
    }
    #sidebar {
        grid-area:sidebar;
    }
    main {
        grid-area: main;
    }
}
</style>
<script setup>
import { provide } from 'vue'

const props = defineProps({
    app: {
        type: Object,
        required: true,
    },
    root: {
        type: Object,
        required: false,
    },
})


const getAppItems = function(app) {
    return [app, ...app.dashboards, ...app.resources, ...app.apps.map(app => getAppItems(app)).flat()]
}


const theme = {
    "rootMenu" : "bg-sky-900 dark:bg-black",
    "menu" :"bg-sky-800 dark:bg-slate-800/60",
    "navbar" : "bg-slate-100 dark:bg-gray-900",
    "bg" : "bg-slate-100 dark:bg-gray-900",
    "cardBg" : "bg-white dark:bg-slate-800/90",
    "tableHeader" :"bg-slate-50 dark:bg-slate-700 dark:text-slate-200",
    "tableBody" : "bg-white dark:bg-slate-800 dark:text-slate-200 text-zinc-500 ",
    "tableRow" : "border-slate-200 dark:border-slate-700 hover:bg-sky-100/20 dark:hover:bg-sky-900/30 ",
    "txtAccent" : "text-sky-400 dark:text-sky-500",
    "accentColor" : 'rgb(56, 189, 248)',// 'rgb(14, 165, 233)', // light 
    "accentColorTransparent" : 'rgba(56, 189, 248, .2)',// 'rgb(14, 165, 233)', // light 

}

const setupTheme = function(name) {
    // if (theme == 'zinc') {
    //     tablebg = 'bg-red-500';
    // }
    provide('theme', theme)
}
setupTheme(props.root.name);
</script>