<template>
    <!-- <a :href="config.link"></a> -->
    <router-link :to="config.link" >{{config.name}}</router-link>
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
        // console.log(this.config)
    }

}

</script>
