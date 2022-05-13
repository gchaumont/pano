import { createApp, h, defineAsyncComponent, reactive } from 'vue'

import PanoComponent from './components/PanoApp'


import Router from './router';

import Pano from './Pano';


var VuePano = reactive(new Pano(document.head.querySelector('meta[name="panoapp-root"]').content))

VuePano.boot(Router)

const app = createApp({
    render: () => h(PanoComponent)
})


app.component('pano-field-table-header', require('./components/fields/TableHeader').default);


app.component('pano-navbar', require('./components/navigation/Navbar').default);
app.component('pano-menu', require('./components/menu/Menu').default);
app.component('pano-menu-item', require('./components/menu/MenuItem').default);
app.component('pano-menu-group', require('./components/menu/MenuGroup').default);

app.config.globalProperties.$pano = VuePano

app.use(Router);

app.mount("#pano-app")
