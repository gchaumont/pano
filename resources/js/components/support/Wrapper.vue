<template>
    <keep-alive>
        <component :is="component" v-bind="data" />
    </keep-alive>
</template>
<script setup>
import { getCurrentInstance, watch, computed } from 'vue'
import { resolveProps } from '@/Pano.js'

const instance = getCurrentInstance();

const props = defineProps({
    component: {
        type: String,
        required: true,
    },
    props: {
        type: Object,
        required: false,
    }
})

const data = computed(() => resolveProps(props.props));


// Check that the component is registered
watch(
    () => props.component,
    () => {
        if (!instance.appContext.app.component(props.component)) {
            console.error("The Component " + props.component + " is not registered");
        }
    }, { immediate: true }
)
</script>