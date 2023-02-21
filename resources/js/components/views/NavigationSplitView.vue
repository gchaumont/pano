<template>
    <div id="mastgrid" class="relative">
        <nav id="sidebar" class="flex">
            <pano-menu v-if="applicationRootMenu" :header="rootApp" :menu="applicationRootMenu"  :class="app.isRoot ? 'bg-sky-800 dark:bg-neutral-900 ' : 'bg-sky-900 dark:bg-black'" :collapsed="!app.isRoot"/>
            <pano-menu :header="app" :menu="app.menu"  class="bg-sky-800 dark:bg-neutral-900" v-if="!app.isRoot"/>
        </nav>
        <pano-navbar id="mastnav"  />
        <main class="flex-1 p-3">
            <router-view v-slot="{ Component }">
                <keep-alive>
                    <component :is="Component" />
                </keep-alive>
            </router-view>
        </main>
    </div>
</template>
<!-- <style type="text/scss" lang="scss">
    #mastgrid {
        display: grid;
        align-items: start;
        max-width: 100vw;
        min-height: 100vh;
        grid-template-columns: min-content 1fr 1fr;
        grid-template-rows: 3.5rem 1fr;
        justify-items: stretch;
        align-items: stretch;
        grid-template-areas:
            "sidebar header header"
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
</style> -->
<script setup>
import { provide, inject } from 'vue' 

const props = defineProps({
    app: {
        type: Object,
        required: true,
    },
})

const rootApp = inject('rootApp');
const applicationRootMenu = inject('applicationRootMenu');
</script>