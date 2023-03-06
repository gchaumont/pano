<template>
    <div class="mb-6">
        <p class="mt-6 text-gray-300 font-medium text-sm mb-2" v-if="!collapsed">{{config.name}}</p>
        <ul class="menu-group-items ">
            <li v-for="item in config.items">
                <component :is="'pano-menu-'+item.type" :config="item" :collapsed="collapsed" />
            </li>
        </ul>
    </div>
</template>
<script setup>
    const props = defineProps({
        config: {
            type: Object,
            required: true,
        },
        collapsed: {
            type: Boolean,
            required: false,
            default: false
        }
    })
</script>