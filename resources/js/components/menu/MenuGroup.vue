<template>
    <a :href="config.link">{{config.name}}</a>
    <ul>
        <li v-for="item in config.items">
            <component :is="'pano-menu-'+item.type" :config="item" />    
        </li>
    </ul>
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
