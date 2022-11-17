<template>
    <template v-if="value">
        <div v-if="value.date">
            <span class="whitespace-nowrap">{{value.date}}</span>
            <template v-if="value.time">
                <br><span class="whitespace-nowrap text-slate-400 dark:text-slate-500">{{value.time}}</span>
            </template>
        </div>
        <span v-else class="whitespace-nowrap">{{value.raw}}</span>
    </template>
</template>
<script>
import Field from './Field';

export default {
    extends: Field,
    props: {
        field: {
            type: Object,
            required: true
        },
        value: {
            required: false
        }
    },
    mounted() {}
}
</script>