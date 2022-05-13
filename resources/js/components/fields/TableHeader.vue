<template>
    <th ><div style="display:flex; align-items: center">
        {{config.name}}
        <div v-if="config.sortable">⬆️⬇️</div>
    </div>
    </th>
</template>
<script>
    export default {
        props: {
            config: {
                type: Object, 
                requried: true
            }
        },
        mounted() {
        }
    }

</script>