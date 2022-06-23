<template>
    {{field}} {{value}}
</template>
<script>
export default {
    props: {
        field: {
            type: Object,
            required: true
        }, 
        value: {
            required: false
        }
    },
    mounted() {
    }
}
</script>