<template>
  <header class="lg:flex lg:items-center lg:justify-between">
    <div class="min-w-0 flex-1">
      <Breadcrumbs v-if="breadcrumbs" :breadcrumbs="breadcrumbs"/> 
      <h2 class=" text-lh font-bold leading-7 text-slate-800 dark:text-white sm:truncate text-2xl sm:text-3xl sm:tracking-tight">{{title}}</h2>
      <ResourceMeta v-if="meta" :meta="meta" /> 
    </div>
    <ResourceActions v-if="actions" :actions="actions" /> 
  </header>
</template>

<script setup>

import Breadcrumbs from '@/components/navigation/Breadcrumbs.vue'
import ResourceMeta from '@/components/resources/ResourceMeta.vue'
import ResourceActions from '@/components/resources/ResourceActions.vue'

const props = defineProps({
  title: {
    required: true, 
    type: String
  }, 
  breadcrumbs: {
    required: false,
    type : Array, 
  },
  meta: {
    required: false, 
    type: Array, 
  }, 
  actions: {
    required: false, 
    type: Array, 
  }
})
</script>