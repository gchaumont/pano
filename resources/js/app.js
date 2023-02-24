import { createApp, h, defineAsyncComponent, reactive } from 'vue'

import '../sass/app.scss';

import PanoRoot from './components/PanoRoot.vue'
import Router from './router';
import { usePano } from './Pano';


const pano = usePano().useRouter(Router)

const app = createApp({
    render: () => h(PanoRoot)
})
.use(Router)
.directive('intersect', {
    mounted(el, binding, vnode) {
        const observer = new IntersectionObserver(([entry]) => binding.value(entry), { threshold: 0, });

        observer.observe(el);
    }
})


import * as heroicons from '@heroicons/vue/24/outline'

for (const property in heroicons) {
    app.component(property, heroicons[property]);
    // app.component(property, defineAsyncComponent(() => import(`@heroicons/vue/24/outline`).then(m => m[property])))
}

import LoadingSpinner from './components/support/LoadingSpinner.vue';
app.component('loading-spinner', LoadingSpinner);

import GlobalSearch from './components/search/GlobalSearch.vue';
app.component('global-search', GlobalSearch);

import HiddenField from './components/fields/HiddenField.vue';
app.component('hidden-field', HiddenField);
import TextField from './components/fields/TextField.vue';
app.component('text-field', TextField);
import NumberField from './components/fields/NumberField.vue';
app.component('number-field', NumberField);
import BadgeField from './components/fields/BadgeField.vue';
app.component('badge-field', BadgeField);
import StackField from './components/fields/StackField.vue';
app.component('stack-field', StackField);
import DateField from './components/fields/DateField.vue';
app.component('date-field', DateField);
import NestedField from './components/fields/NestedField.vue';
app.component('nested-field', NestedField);
import ThumbnailField from './components/fields/ThumbnailField.vue';
app.component('thumbnail-field', ThumbnailField);
import IconField from './components/fields/IconField.vue';
app.component('icon-field', IconField);

import RelationField from './components/fields/RelationField.vue';
app.component('relates-to-one-field', RelationField);


import TableHeader from './components/fields/TableHeader.vue';
app.component('pano-field-table-header', TableHeader);
import TableFooter from './components/resources/TableFooter.vue';
app.component('pano-table-footer', TableFooter);
import ModelSearch from './components/resources/ModelSearch.vue';
app.component('model-search', ModelSearch);

import DataTable from './components/tables/DataTable.vue';
app.component('data-table', DataTable);

import HeroComponent from './components/support/HeroComponent.vue';
app.component('hero-component', HeroComponent);


import ValueMetric from './components/metrics/ValueMetric.vue';
app.component('value-metric', ValueMetric);

import PartitionMetric from './components/metrics/PartitionMetric.vue';
app.component('partition-metric', PartitionMetric);
import TrendMetric from './components/metrics/TrendMetric.vue';
app.component('trend-metric', TrendMetric);


import Navbar from './components/navigation/Navbar.vue';
app.component('pano-navbar', Navbar);
import Menu from './components/menu/Menu.vue';
app.component('pano-menu', Menu);
import MenuItem from './components/menu/MenuItem.vue';
app.component('pano-menu-item', MenuItem);
import MenuGroup from './components/menu/MenuGroup.vue';
app.component('pano-menu-group', MenuGroup);


import Dashboard from './components/dashboards/Dashboard.vue';
app.component('Dashboard', Dashboard);

import AppHome from './components/AppHome.vue';
app.component('AppHome', AppHome);

import PanoApplication from './components/applications/PanoApplication.vue';
app.component('PanoApplication', PanoApplication);

import NavigationSplitView from './components/views/NavigationSplitView.vue';
app.component('NavigationSplitView', NavigationSplitView);

// import ResourceSkeleton from './components/resources/ResourceSkeleton.vue';
// app.component('ResourceSkeleton', ResourceSkeleton);

import ListResource from './components/resources/ListResource.vue';
app.component('ListResource', ListResource)

;import ShowResource from './components/resources/ShowResource.vue';
app.component('ShowResource', ShowResource);

app.mount("#pano-app")