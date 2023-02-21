<template>
    <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
        <div class="mt-2 flex items-center text-sm dark:text-gray-300 text-gray-500">
            <BriefcaseIcon class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400 " aria-hidden="true" />
            Full-time
        </div>
        <div class="mt-2 flex items-center text-sm dark:text-gray-300 text-gray-500">
            <MapPinIcon class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400 " aria-hidden="true" />
            Remote
        </div>
        <div class="mt-2 flex items-center text-sm dark:text-gray-300 text-gray-500">
            <CurrencyDollarIcon class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400 " aria-hidden="true" />
            $120k – $140k
        </div>
        <div class="mt-2 flex items-center text-sm dark:text-gray-300 text-gray-500">
            <CalendarIcon class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400 " aria-hidden="true" />
            Closing on January 9, 2020
        </div>
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
const props = defineProps({
    meta: {
        required: true,
        type: Array,
    }
})
</script>