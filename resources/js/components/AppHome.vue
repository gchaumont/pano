<template>
    APPLICATION HOME
    <pano-navbar />
    <pano-menu :config="$pano.menu" />
    <router-view />
</template>
<script>
export default {
    props: {
        app: {
            type: String,
            required: false, 
        }
    },
    computed: {
        menu : function () {
            // if (this.app) {
            //     return this.$pano.apps
            // }


        }
    },
    mounted() {
        console.log('APPLICATION', this.app, console.log(this.$pano))


    }
}

</script>
