<template>
    <ul>
        <li v-for="menu in config">
            <component :is="'pano-menu-'+menu.type" :config="menu"/>
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
    },
    mounted() {
        console.log(this.config)
    }
}

</script>
