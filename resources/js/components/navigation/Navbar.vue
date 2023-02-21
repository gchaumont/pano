<template>
    <header id="navbar-title" :class="[theme.navbar , 'h-16 flex flex-1 justify-between px-4 p-2']">
        <div class="hidden lg:ml-6 lg:block">
            <div class="flex space-x-4 p-1">

                <!-- <router-link v-for="item in app.sections" :to="item.path"  
                class="rounded-md px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white" active-class="bg-gray-100 text-gray-900 dark:bg-gray-800 dark:hover:bg-gray-800 dark:text-white hover:bg-gray-100"> {{item.name}}</router-link> -->
            </div>
        </div>
        <!--      <form class="flex w-full md:ml-0" action="#" method="GET">
                <label for="search-field" class="sr-only">Search</label>
                <div class="relative w-full text-gray-400 focus-within:text-gray-600">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center">
                        <MagnifyingGlassIcon class="h-5 w-5" aria-hidden="true" />
                    </div>
                    <input id="search-field" class="block h-full w-full border-transparent py-2 pl-8 pr-3 text-gray-900 placeholder-gray-500 focus:border-transparent focus:placeholder-gray-400 focus:outline-none focus:ring-0 sm:text-sm dark:bg-gray-900" placeholder="Search" type="search" name="search" />
                </div>
            </form> -->
        <div class="ml-4 flex items-center md:ml-6">
            <button type="button" class="rounded-full p-1 mx-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" @click="openSearch">
                <MagnifyingGlassIcon class="h-5 w-5" aria-hidden="true" />
            </button>
            <button type="button" class="rounded-full p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <span class="sr-only">View notifications</span>
                <BellIcon class="h-6 w-6" aria-hidden="true" />
            </button>
            <!-- Profile dropdown -->
            <Menu as="div" class="relative ml-3">
                <div>
                    <MenuButton class="flex max-w-xs items-center rounded-full bg-white dark:bg-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <span class="sr-only">Open user menu</span>
                        <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name=G+C&color=FFFFFF&background=111827" alt="" />
                    </MenuButton>
                </div>
                <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                    <MenuItems class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                        <MenuItem v-for="item in userNavigation" :key="item.name" v-slot="{ active }">
                        <a :href="item.href" :class="[active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700']">{{ item.name }}</a>
                        </MenuItem>
                    </MenuItems>
                </transition>
            </Menu>
        </div>
    </header>
</template>
<script setup>
import { ref , inject} from 'vue'
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

import {
    MagnifyingGlassIcon
} from '@heroicons/vue/20/solid'

const theme = inject('theme')

const userNavigation = [
    { name: 'Your Profile', href: '#' },
    { name: 'Settings', href: '#' },
    { name: 'Sign out', href: '#' },
]

const openSearch = function() {
    window.dispatchEvent(new Event("startSearch"))
}

</script>
