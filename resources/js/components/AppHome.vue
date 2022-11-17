<template>
    <!-- class="flex flex-row gap-2 min-h-screen " -->
    <div id="mastgrid">
        <aside class="sticky top-0 h-full p-2">
            <h1>
                <router-link class="text-slate-700 dark:text-slate-300 ml-2 mt-2 text-3xl" :to="app.path">{{app.name}}</router-link>
            </h1>
            <pano-menu :app="app" :config="app.menu" :parent="parent" />
        </aside>
        <main class="flex-1">
            <pano-navbar class="mastnav" :app="app" />
            <router-view v-slot="{ Component }">
                <keep-alive>
                    <component :is="Component" />
                </keep-alive>
            </router-view>
        </main>
        <global-search :app="$pano.rootApp" />
    </div>
</template>
<style type="text/scss" lang="scss">
    #mastgrid {
        display: grid;
        align-items: start;
        max-width: 100vw;
        min-height: 100vh;
        grid-template-columns: minmax(30ch, 30ch) 1fr 1fr;
        grid-template-rows: 3.5rem 1fr;
        justify-items: stretch;
        align-items: stretch;
        grid-template-areas:
            "home main main"
            "menu main main";

    ;

     .mastnav {
        grid-area: header;
    }
    main {
        grid-area: main;
        }
    }


</style>
<script>
export default {
    props: {
        app: {
            type: Object,
            required: true,
        },
        parent: {
            type: Object,
            required: false,
        },
    },
    mounted() {

    }
}
</script>