<template>
    <TransitionRoot :show="open" as="template" @after-leave="rawQuery = ''" appear>
        <Dialog as="div" class="relative z-10" @close="open = false">
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-25 transition-opacity" />
            </TransitionChild>
            <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20">
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 scale-95" enter-to="opacity-100 scale-100" leave="ease-in duration-200" leave-from="opacity-100 scale-100" leave-to="opacity-0 scale-95">
                    <DialogPanel class="mx-auto max-w-xl transform divide-y divide-gray-100 overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all">
                        <Combobox @update:modelValue="onSelect">
                            <div class="relative">
                                <MagnifyingGlassIcon class="pointer-events-none absolute top-3.5 left-4 h-5 w-5 text-gray-400" aria-hidden="true" />
                                <ComboboxInput class="h-12 w-full border-0 bg-transparent pl-11 pr-4 text-gray-800 placeholder-gray-400 focus:ring-0 sm:text-sm outline-none" placeholder="Search..." @change="rawQuery = $event.target.value" />
                            </div>
                            <ComboboxOptions v-if="filteredResults.length > 0" static class="max-h-80 scroll-py-10 scroll-pb-2 space-y-4 overflow-y-auto p-4 pb-2">
                                <li v-if="filteredResults.length > 0">
                                    <h2 class="text-xs font-semibold text-gray-900">Results</h2>
                                    <ul class="-mx-4 mt-2 text-sm text-gray-700">
                                        <ComboboxOption v-for="result in filteredResults" :key="result.key" :value="result" as="template" v-slot="{ active }">
                                            <li :class="['flex cursor-default select-none items-center px-4 py-2', active && 'bg-sky-600 text-white']">
                                                <component v-if="result.icon" :is="result.icon" :class="['h-6 w-6 flex-none', active ? 'text-white' : 'text-gray-400']" aria-hidden="true" />
                                                <FolderIcon v-else :class="['h-6 w-6 flex-none', active ? 'text-white' : 'text-gray-400']" aria-hidden="true" />

                                                <span class="ml-3 flex-auto truncate">{{ result.name }}</span>
                                                <!-- <Breadcrumbs v-if="active" :breadcrumbs="result.breadcrumbs" /> -->
                                            </li>
                                        </ComboboxOption>
                                    </ul>
                                </li>
                            </ComboboxOptions>
                            <div v-if="rawQuery === '?'" class="py-14 px-6 text-center text-sm sm:px-14">
                                <LifebuoyIcon class="mx-auto h-6 w-6 text-gray-400" aria-hidden="true" />
                                <p class="mt-4 font-semibold text-gray-900">Help with searching</p>
                                <p class="mt-2 text-gray-500">Use this tool to quickly search for users and projects across our entire platform. You can also use the search modifiers found in the footer below to limit the results to just users or projects.</p>
                            </div>
                            <div v-if="query !== '' && rawQuery !== '?' && filteredResults.length === 0 " class="py-14 px-6 text-center text-sm sm:px-14">
                                <ExclamationTriangleIcon class="mx-auto h-6 w-6 text-gray-400" aria-hidden="true" />
                                <p class="mt-4 font-semibold text-gray-900">No results found</p>
                                <p class="mt-2 text-gray-500">We couldn’t find anything with that term. Please try again.</p>
                            </div>
                            <div class="flex flex-wrap items-center bg-gray-50 py-2.5 px-4 text-xs text-gray-700">
                                Type
                                <kbd :class="['mx-1 flex h-5 w-5 items-center justify-center rounded border bg-white font-semibold sm:mx-2', rawQuery.startsWith('#') ? 'border-sky-600 text-sky-600' : 'border-gray-400 text-gray-900']">#</kbd>
                                <span class="sm:hidden">for projects,</span>
                                <span class="hidden sm:inline">to access projects,</span>
                                <kbd :class="['mx-1 flex h-5 w-5 items-center justify-center rounded border bg-white font-semibold sm:mx-2', rawQuery.startsWith('>') ? 'border-sky-600 text-sky-600' : 'border-gray-400 text-gray-900']">&gt;</kbd>
                                for users, and
                                <kbd :class="['mx-1 flex h-5 w-5 items-center justify-center rounded border bg-white font-semibold sm:mx-2', rawQuery === '?' ? 'border-sky-600 text-sky-600' : 'border-gray-400 text-gray-900']">?</kbd>
                                for help.
                            </div>
                        </Combobox>
                    </DialogPanel>
                </TransitionChild>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
<script setup>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { MagnifyingGlassIcon } from '@heroicons/vue/20/solid'
import { ExclamationTriangleIcon, FolderIcon, LifebuoyIcon } from '@heroicons/vue/24/outline'
import Breadcrumbs from '@/components/navigation/Breadcrumbs.vue'

import {
    Combobox,
    ComboboxInput,
    ComboboxOptions,
    ComboboxOption,
    Dialog,
    DialogPanel,
    TransitionChild,
    TransitionRoot,
} from '@headlessui/vue'

const router = useRouter();
const open = ref(false)
const rawQuery = ref('')
const query = computed(() => rawQuery.value.toLowerCase().replace(/^[#>]/, ''))
// const filteredResults = computed(() =>
//     rawQuery.value === '#' ?
//     projects :
//     query.value === '' || rawQuery.value.startsWith('>') ?
//     [] :
//     projects.filter((result) => result.name.toLowerCase().includes(query.value))
// )



const props = defineProps({
    items: {
        type: Array,
        required: true,
    }
})

window.addEventListener('startSearch', () => open.value = true);
function onSelect(item) {
    router.push(item.path)
    open.value = false
}

const filteredResults = computed(() => {
    return props.items.filter(item => item.name && item.name.toLowerCase().includes(query.value));
})



const _keyListener = function(e) {
    // if (e.key === "Escape") {
    //     if (this.active) {
    //         e.preventDefault();
    //         this.active = false;
    //     }
    // }
    // if (e.key === "Enter") {
    //     if (this.active) {
    //         e.preventDefault();
    //         this.selectItem(this.selected);
    //     }
    // }
    if (e.key === "k" && (e.ctrlKey || e.metaKey)) {
        e.preventDefault();
        // this.toggle();
        open.value = !open.value;
    }
    // if (e.key === "ArrowDown") {
    //     e.preventDefault();
    //     if (this.selected >= this.maxItems-1) {
    //         this.selected = 0;
    //      } else {
    //         this.selected = this.selected + 1;
    //      }
    // }
    // if (e.key === "ArrowUp") {
    //     e.preventDefault();
    //     if (this.selected <= 0) {
    //         this.selected = this.maxItems-1;
    //     } else {
    //         this.selected = this.selected-1;
    //     }
    // }
};
document.addEventListener('keydown', _keyListener);


</script>
<!-- <template>
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
                                <span class="muted">\</span>
                                <span>item.name</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </transition>
        </div>
    </transition>
</template>
<script>
import { MagnifyingGlassIcon } from '@heroicons/vue/24/solid'
export default {
    components: { MagnifyingGlassIcon },
    props: ,
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
        
    },
    methods: {
        selectItem: function(index) {
            this.$router.push(this.searchItems[index].path)
            this.deactivate()
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
    },
    mounted() {
        this.$watch(
            () => this.query,
            () => this.selected = 0,
        );


        

    },
    beforeDestroy() {
        document.removeEventListener('keydown', this._keyListener);
    }
}
</script> -->