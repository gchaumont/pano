import { createRouter, createWebHistory } from 'vue-router'

import PanoSkeleton from './components/PanoSkeleton'
import AppHome from './components/AppHome'
import ResourceSkeleton from './components/Resources/ResourceSkeleton'
import ListResource from './components/Resources/ListResource'
import ShowResource from './components/PanoApp'
import EditResource from './components/PanoApp'
import CreateResource from './components/PanoApp'


export function resourceRoutes(path, resources) {
    var keys = resources.join('|');

    return {
        component: AppHome,
        path: path + '/:resource(' + keys + ')',
        children: [{
            component: ListResource,
            path: '',
            props: true,
        }, {
            component: ShowResource,
            path: '{object}',
            props: true,

        }, {
            component: EditResource,
            path: '{object}/edit', 
            props: true,

        }, {
            component: CreateResource,
            path: 'create',
            props: true
        }]
    }
}

export function appRoutes(path, app) {
    return {
        component: AppHome,
        path: path,
        props: {app:app}
    }

}

export function dashboardRoutes(dashboards) {
    var keys = dashboards.join('|');
    return {
        component: PanoSkeleton,
        path: '{resource(' + keys + ')}',
    }
}




// const routes = [{
//     path: '/' + document.head.querySelector('meta[name="panoapp-root"]').content,
//     component: PanoSkeleton,
// }]
const routes = [];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition
        }
        if (to.hash) {
            return {
                el: to.hash,
                behavior: 'smooth',
            }
        }
        return { top: 0 }
    },
})
export default router
