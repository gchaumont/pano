<template>
    <transition enter-active-class="duration-200 ease-in-out " enter-from-class="transform opacity-0" leave-active-class="duration-200 ease-in-out" enter-to-class="opacity-100" leave-from-class="opacity-100" leave-to-class="transform opacity-0">
        <div v-show="active" class="fixed inset-0 flex items-start justify-center z-50 bg-backdrop backdrop-blur-sm" @click="deactivate()">
            <transition enter-active-class="duration-150 ease-in-out " enter-from-class="transform opacity-0 scale-95" leave-active-class="duration-150 ease-in-out" enter-to-class="opacity-100 scale-100" leave-from-class="opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95" >
                <div v-show="active" :class="{'mt-24 bg-white dark:bg-slate-800 min-w-55ch rounded-lg text-slate-300  ' : true, '' : !active} ">
                    <div class="flex items-center p-4 px-6 ">
                        <MagnifyingGlassIcon class="h-6 w-6 text-slate-400" />
                        <input id="glob-search-input" class="w-full pl-3 text-slate-700 dark:text-slate-400 border-0 outline-none bg-transparent   text-lg placeholder:text-slate-500 dark:placeholder:text-slate-400" type="text" v-model="query" placeholder="Search..." autocomplete="off" autocapitalize="off" autocorrect="off" spellcheck="false" enterKeyHint="search">
                        <button type="reset" class="shadow-sm text-2xs rounded-lg font-semibold text-slate-600 border border-slate-100 hover:border-slate-200 dark:border-none dark:text-slate-400 dark:bg-slate-600  dark:hover:text-slate-300   px-1.5 py-1.5" label="Cancel">ESC</button>
                    </div>
                    <ul class="border-t p-2 border-slate-300 dark:border-slate-600">
                        <li class="" v-for="item, i in searchItems" v-show="i<maxItems" @click="selectItem(i)" @mouseenter="selected = i">
                            <div :class="{'flex rounded-md items-center gap-5 px-4 py-2 cursor-pointer  ' : true, 'text-slate-800 dark:text-slate-300 bg-slate-100 dark:bg-slate-900/80': i==selected , 'text-slate-500 dark:text-slate-400':i!= selected}">
                                <!-- <object-icon :object="item.item" /> -->
                                <!-- <template v-for="div in item.item.path.split(':')"> -->
                                <!-- <span v-html="highlight(item, 'path')" ></span> -->
                                <span class="muted">\</span>
                                <!-- </template> -->
                                <!-- <span v-html="highlight(item, 'name')"></span> -->
                                <span>{{item.name}}</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </transition>
        </div>
    </transition>
</template>
<style lang="scss">
.hibold {
    font-weight: 700;
}
</style>
<script>
import { MagnifyingGlassIcon } from '@heroicons/vue/24/solid'
export default {
    components: { MagnifyingGlassIcon },
    props: {
        app: {
            type: Object,
            required: true,
        }
    },
    data() {
        return {
            active: false,
            query: '',
            selected: 0,
            maxItems: 10,
            _keyListener: null
        }
    },
    computed: {
        searchItems: function() {
            return this.getAppItems(this.app).filter(item => item.name && item.name.toLowerCase().includes(this.query.toLowerCase()));
        },
    },
    methods: {
        selectItem: function(index) {
            this.$router.push(this.searchItems[index].path)
            this.deactivate()
        },
        getAppItems: function(app) {
            return [...app.dashboards, ...app.resources, ...app.apps.map(app => this.getAppItems(app)).flat()]
        },
        activate: function() {
            this.active = true;
            this.selected = 0;

            setTimeout(() => document.getElementById('glob-search-input').focus(), 20)
        },
        deactivate: function() {
            this.active = false;
            this.query = '';
        },
        toggle: function() {
            if (this.active) {
                this.deactivate()
            } else {
                this.activate();
            }
        },
        highlight: function(object, key) {

            var innerHTML = object.item[key];
            // var innerHTML = string;

            for (var i = object.matches.length - 1; i >= 0; i--) {
                if (object.matches[i].key == key) {
                    for (var y = object.matches[i].indices.length - 1; y >= 0; y--) {
                        if (object.matches[i].indices[y][1] - object.matches[i].indices[y][0] >= 2) {

                            return innerHTML.substring(0, object.matches[i].indices[y][0]) +
                                "<span class='hibold'>" +
                                innerHTML.substring(object.matches[i].indices[y][0], object.matches[i].indices[y][1] + 1) +
                                "</span>" +
                                innerHTML.substring(object.matches[i].indices[y][1] + 1)

                        }
                    }
                }
            }
            return innerHTML;

        }
    },
    mounted() {
        this.$watch(
            () => this.query,
            () => this.selected = 0,
        );


        this._keyListener = function(e) {
            if (e.key === "Escape") {
                if (this.active) {
                    e.preventDefault();
                    this.active = false;
                }
            }
            if (e.key === "Enter") {
                if (this.active) {
                    e.preventDefault();
                    this.selectItem(this.selected);
                }
            }
            if (e.key === "k" && (e.ctrlKey || e.metaKey)) {
                e.preventDefault();
                this.toggle();
            }
            if (e.key === "ArrowDown") {
                e.preventDefault();
                if (this.selected >= this.maxItems-1) {
                    this.selected = 0;
                 } else {
                    this.selected = this.selected + 1;
                 }
            }
            if (e.key === "ArrowUp") {
                e.preventDefault();
                if (this.selected <= 0) {
                    this.selected = this.maxItems-1;
                } else {
                    this.selected = this.selected-1;
                }
            }


        };
        document.addEventListener('keydown', this._keyListener.bind(this));

    },
    beforeDestroy() {
        document.removeEventListener('keydown', this._keyListener);
    }
}
</script>