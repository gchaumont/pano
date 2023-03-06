<template>
  <Listbox as="div" v-model="selected" class="mt-3">
    <!-- <ListboxLabel class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assigned to</ListboxLabel> -->
    <div class="relative mt-1">
      <ListboxButton class="relative w-full cursor-default rounded-md border border-slate-300 dark:border-gray-600 bg-gray-400/30 dark:bg-slate-700 py-2 pl-3 pr-10 text-white text-left shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 sm:text-sm">
        <span class="block truncate ">{{ selected.label }}</span>
        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
          <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
        </span>
      </ListboxButton>

      <transition leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <ListboxOptions class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white dark:bg-slate-600  py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
          <ListboxOption as="template" v-for="option in options" :key="option.value" :value="option" v-slot="{ active, selected }">
            <li :class="[active ? 'text-white bg-sky-600 dark:bg-sky-600' : 'text-gray-900 dark:text-gray-200', 'relative cursor-default select-none py-2 pl-3 pr-9']">
              <span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">{{ option.label }}</span>

              <span v-if="selected" :class="[active ? 'text-white' : 'text-sky-600', 'absolute inset-y-0 right-0 flex items-center pr-4']">
                <CheckIcon class="h-5 w-5" aria-hidden="true" />
              </span>
            </li>
          </ListboxOption>
        </ListboxOptions>
      </transition>
    </div>
  </Listbox>
</template>

<script setup>

const props = defineProps({
    config: {
        type: Object,
        required: true,
    },
})

import { ref, watch } from 'vue'
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid'

const options = ref(props.config.props.options)

// const options = [
//   { id: 1, name: 'Wade Cooper' },
//   { id: 2, name: 'Arlene Mccoy' },
//   { id: 3, name: 'Devon Webb' },
//   { id: 4, name: 'Tom Cook' },
//   { id: 5, name: 'Tanya Fox' },
//   { id: 6, name: 'Hellen Schmidt' },
//   { id: 7, name: 'Caroline Schultz' },
//   { id: 8, name: 'Mason Heaney' },
//   { id: 9, name: 'Claudie Smitham' },
//   { id: 10, name: 'Emil Schaefer' },
// ]

const selected = ref(options.value[0])

watch(selected, (value) => {
    // window.alert('Tenant selected'+  value)
})

</script>

