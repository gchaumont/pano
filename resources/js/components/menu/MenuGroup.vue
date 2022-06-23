<template>
    <div class="mt-6">
        <p class="text-gray-500 uppercase font-bold text-xs mb-2">{{config.name}}</p>
        <ul  class="menu-group-items">
            <li v-for="item in config.items">
                <component :is="'pano-menu-'+item.type" :config="item" />
            </li>
        </ul>
    </div>
</template>
<script>
export default {
    props: {
        config: {
            type: Object,
            required: true,
        }
    }

}
</script>