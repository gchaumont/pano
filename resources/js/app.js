import { createApp, h, defineAsyncComponent, reactive } from 'vue'

import PanoComponent from './components/PanoApp'


import Router from './router';

import Pano from './Pano';


var VuePano = reactive(new Pano(document.head.querySelector('meta[name="panoapp-root"]').content))

VuePano.boot(Router)

const app = createApp({
    render: () => h(PanoComponent)
})

app.directive('intersect', {
    mounted(el, binding, vnode) {
        const observer = new IntersectionObserver(([entry]) => binding.value(entry), { threshold: 0, });

        observer.observe(el);
    }
})


import * as heroicons from '@heroicons/vue/24/outline'

for (const property in heroicons) {
        // app.component(property, heroicons[property]);
    app.component(property, defineAsyncComponent(() => import( /* webpackMode: 'lazy', webpackChunkName: 'heroicons/[request]' */ `@heroicons/vue/24/outline`).then(m => m[property])))
}

app.component('loading-spinner', require('./components/support/LoadingSpinner').default);

app.component('global-search', require('./components/search/GlobalSearch').default);

app.component('hidden-field', require('./components/fields/HiddenField').default);
app.component('text-field', require('./components/fields/TextField').default);
app.component('number-field', require('./components/fields/NumberField').default);
app.component('badge-field', require('./components/fields/BadgeField').default);
app.component('stack-field', require('./components/fields/StackField').default);
app.component('date-field', require('./components/fields/DateField').default);
app.component('nested-field', require('./components/fields/NestedField').default);
// app.component('relation-field', require('./components/fields/RelationField').default);
app.component('relates-to-one-field', require('./components/fields/RelationField').default);


app.component('pano-field-table-header', require('./components/fields/TableHeader').default);
app.component('pano-table-footer', require('./components/resources/TableFooter').default);
app.component('model-search', require('./components/resources/ModelSearch').default);

app.component('data-table', require('./components/tables/DataTable').default);


app.component('value-metric', require('./components/metrics/ValueMetric').default);
app.component('partition-metric', require('./components/metrics/PartitionMetric').default);
app.component('trend-metric', require('./components/metrics/TrendMetric').default);


app.component('pano-navbar', require('./components/navigation/Navbar').default);
app.component('pano-menu', require('./components/menu/Menu').default);
app.component('pano-menu-item', require('./components/menu/MenuItem').default);
app.component('pano-menu-group', require('./components/menu/MenuGroup').default);

app.config.globalProperties.$pano = VuePano

app.use(Router);

app.mount("#pano-app")
