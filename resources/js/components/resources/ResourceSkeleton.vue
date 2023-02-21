<template>
    <router-view :resource="resource" />
</template>
<script setup>
const props = defineProps({
    resource: {
        required: true, 
        type: Object
    }
})

</script>