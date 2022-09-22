import { createRouter, createWebHistory } from 'vue-router'

import PanoSkeleton from './components/PanoSkeleton'
import AppHome from './components/AppHome'
import ResourceSkeleton from './components/Resources/ResourceSkeleton'
import ListResource from './components/Resources/ListResource'
import Dashboard from './components/Dashboards/Dashboard'
import ShowResource from './components/Resources/ShowResource'
import EditResource from './components/PanoApp'
import CreateResource from './components/PanoApp'


export function resourceRoutes(resource, app, parent) {
    return {
        component: AppHome,
        path: resource.path,
        name: resource.route,
        props: { app, parent },
        children: [{
            component: ListResource,
            path: '',
            props: { resource },

        }, {
            component: ShowResource,
            path: ':object',
            props: route => ({ resource, object: route.params.object }),
        }, {
            component: EditResource,
            path: ':object/edit',
            props: { resource },
        }, {
            component: CreateResource,
            path: 'create',
            props: { resource },
        }]
    }
}

export function appRoutes(app, parent) {
    var props = { app }
    if (parent) {
        props.parent = parent;
    }
    return {
        redirect: app.homepage,
        path: app.path,
        name: app.route,
        props: props
    }

}

export function dashboardRoutes(dashboard, app, parent) {
    return {
        component: AppHome,
        path: dashboard.path,
        name: dashboard.route,
        props: { app, parent },
        children: [{
            component: Dashboard,
            path: '',
            props: { dashboard },
        }, ]
    }
}

const routes = [];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition
        }
        if (from.path === to.path) {
            return {}
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