<template>
    <div id="mastgrid" class="flex flex-row gap-2 min-h-screen ">
        <aside class="sticky top-0 h-full p-2">
            <h1>
                <router-link class="text-slate-700 dark:text-slate-300 ml-2 mt-2 text-3xl" :to="app.path">{{app.name}}</router-link>
            </h1>
            <pano-menu :app="app" :config="app.menu" :parent="parent" />
        </aside>
        <main class="w-full">
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