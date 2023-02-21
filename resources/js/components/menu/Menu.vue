<template>
<!--     <TransitionRoot as="template" :show="sidebarOpen">
        <Dialog as="div" class="relative z-40 md:hidden" @close="sidebarOpen = false">
            <TransitionChild as="template" enter="transition-opacity ease-linear duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="transition-opacity ease-linear duration-300" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-600 bg-opacity-75" />
            </TransitionChild>
            <div class="fixed inset-0 z-40 flex">
                <TransitionChild as="template" enter="transition ease-in-out duration-300 transform" enter-from="-translate-x-full" enter-to="translate-x-0" leave="transition ease-in-out duration-300 transform" leave-from="translate-x-0" leave-to="-translate-x-full">
                    <DialogPanel class="relative flex w-full max-w-xs flex-1 flex-col bg-gray-800 pt-5 pb-4">
                        <TransitionChild as="template" enter="ease-in-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in-out duration-300" leave-from="opacity-100" leave-to="opacity-0">
                            <div class="absolute top-0 right-0 -mr-12 pt-2">
                                <button type="button" class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" @click="sidebarOpen = false">
                                    <span class="sr-only">Close sidebar</span>
                                    <XMarkIcon class="h-6 w-6 text-white" aria-hidden="true" />
                                </button>
                            </div>
                        </TransitionChild>
                        <div class="flex flex-shrink-0 items-center px-4">
                            <img class="h-8 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=500" alt="Your Company" />
                        </div>
                        <div class="mt-5 h-0 flex-1 overflow-y-auto">
                            <nav class="space-y-1 px-2">
                                <a v-for="item in navigation" :key="item.name" :href="item.href" :class="[item.current ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white', 'group flex items-center px-2 py-2 text-base font-medium rounded-md']">
                                    <component :is="item.icon" :class="[item.current ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300', 'mr-4 flex-shrink-0 h-6 w-6']" aria-hidden="true" />
                                    {{ item.name }}
                                </a>
                            </nav>
                        </div>
                    </DialogPanel>
                </TransitionChild>
                <div class="w-14 flex-shrink-0" aria-hidden="true">
                    Dummy element to force sidebar to shrink to fit close icon
                </div>
            </div>
        </Dialog>
    </TransitionRoot> -->
    <div  :class="[width ,'hidden h-screen md:flex sticky top-0  md:flex-col overflow-x-auto' ]">
        <!-- md:w-64  md:fixed md:inset-y-0 -->
        <!-- Sidebar component, swap this element with another sidebar if you like -->
        <div class="flex  h-full flex-1 flex-col  ">
            <router-link :to="header.path" class="flex h-16 flex-shrink-0 items-center px-3 ">
                <!-- <img class="h-8 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=500" :alt="header.name" /> -->
                <img v-if="header.logo" :src="header.logo" alt="logo" class="block h-8 w-8 p-0.5  object-contain bg-white rounded">
                <h1 v-if="!collapsed" class=" text-white dark:text-slate-300 font-medium mx-3 text-xl">{{header.name}}
                    
                </h1>
            </router-link>
            <div class="flex flex-1 flex-col overflow-y-auto px-2 py-4">
                <nav class="flex-1 ">
                    <component v-for="item in menu" :is="'pano-menu-'+item.type" :config="item" :collapsed="collapsed"/>
                </nav>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, inject, computed } from 'vue'
import {
    Dialog,
    DialogPanel,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    TransitionChild,
    TransitionRoot,
} from '@headlessui/vue'
import {
    Bars3BottomLeftIcon,
    BellIcon,
    CalendarIcon,
    ChartBarIcon,
    FolderIcon,
    HomeIcon,
    InboxIcon,
    UsersIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline'
import { MagnifyingGlassIcon } from '@heroicons/vue/20/solid'
import { ArrowLeftIcon } from '@heroicons/vue/24/solid';


const sidebarOpen = ref(false)

const props = defineProps({
    components: { ArrowLeftIcon },
    header: {
        type: Object,
        required: true,
    },
    menu: {
        type: Object,
        required: true,
    },
    collapsed: {
        type: Boolean, 
        required: false, 
        defalut: false
    }
})



const width = computed(() => {
    return props.collapsed ? 'w-fit' : 'w-full'
})

</script>