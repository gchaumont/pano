<template>
    <div class="mt-5 flex lg:mt-0 lg:ml-4">
        <span class="hidden sm:block">
            <button type="button" :class="secondaryButtonClass">
                <PencilIcon class="-ml-1 mr-2 h-5 w-5 dark:text-gray-300 text-gray-500" aria-hidden="true" />
                Edit
            </button>
        </span>
        <span class="ml-3 hidden sm:block">
            <button type="button" :class="secondaryButtonClass">
                <LinkIcon class="-ml-1 mr-2 h-5 w-5 dark:text-gray-300 text-gray-500" aria-hidden="true" />
                View
            </button>
        </span>
        <span class="sm:ml-3">
            <button type="button" :class="primaryButtonClass">
                <CheckIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                Publish
            </button>
        </span>
        <!-- Dropdown -->
        <Menu as="div" class="relative ml-3 sm:hidden">
            <MenuButton class="inline-flex items-center rounded-md border border-transparent bg-gray-700 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-gray-800">
                More
                <ChevronDownIcon class="-mr-1 ml-2 h-5 w-5 text-gray-400" aria-hidden="true" />
            </MenuButton>
            <transition enter-active-class="transition ease-out duration-200" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                <MenuItems class="absolute left-0 z-10 mt-2 -ml-1 w-48 origin-top-left rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                    <MenuItem v-slot="{ active }">
                    <a href="#" :class="[active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700']">Edit</a>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                    <a href="#" :class="[active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700']">View</a>
                    </MenuItem>
                </MenuItems>
            </transition>
        </Menu>
    </div>
</template>
<script setup>
import {
    BriefcaseIcon,
    CalendarIcon,
    CheckIcon,
    ChevronDownIcon,
    CurrencyDollarIcon,
    LinkIcon,
    MapPinIcon,
    PencilIcon,
} from '@heroicons/vue/20/solid'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'

const props = defineProps({
    actions: {
        required: true,
        type: Array,
    }
});

const baseButton ="inline-flex items-center rounded-md border border-gray-300  dark:border-transparent focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-gray-800 px-4 py-2  text-sm font-medium  text-gray-700 dark:text-white shadow-sm"


const secondaryButtonClass =baseButton+" bg-white hover:bg-gray-50  dark:bg-gray-600   dark:hover:bg-gray-700"

const primaryButtonClass =baseButton+" text-white bg-sky-600 dark:bg-sky-500  hover:bg-sky-700 dark:hover:bg-sky-600"
</script>