import { createRouter, createWebHistory } from 'vue-router'

const AppHome = () => import('./components/AppHome.vue')
const ResourceSkeleton = () => import('./components/resources/ResourceSkeleton.vue')
const ListResource = () => import('./components/resources/ListResource.vue')
const Dashboard = () => import('./components/dashboards/Dashboard.vue')
const ShowResource = () => import('./components/resources/ShowResource.vue')
const EditResource = () => import('./components/PanoRoot.vue')
const CreateResource = () => import('./components/PanoRoot.vue')
const NotFound = () => import('./components/NotFound.vue')

export function resourceRoutes(resource, app, root) {
    var routes =  [{
        component: ListResource,
        path: '',
        name: resource.route,
        props: { app, root, resource },
    }, {
        component: ShowResource,
        path: resource.path+'/:record',
        props: route => ({ resource, record: route.params.record, app, root }),
    }, {
        component: EditResource,
        path: resource.path+'/:record/edit',
        props: route => ({ app, root, resource, record: route.params.record }),
    }, {
        component: CreateResource,
        path: resource.path+'/create',
        props: { app, root, resource },
    }]

    return  [{
        path: resource.path, 
        children: routes,
    }]
}

export function appRoutes(app, root) {



    return [{
            component: AppHome,
            path: app.path,
            redirect: app.homepage,
            props: { app, root },
            children: [
                ...app.dashboards.map(dash => dashboardRoute(dash, app, root)),
                ...app.resources.map(dash => resourceRoutes(dash, app, root)).flat()
            ]
        },
        ...app.apps.map(child => appRoutes(child, root)).flat()
    ]




    return {
        redirect: app.homepage,
        path: app.path,
        name: app.route,
        props: props
    }
}

export function dashboardRoute(dashboard, app, root) {
    return {
        component: Dashboard,
        path: dashboard.path,
        name: dashboard.route,
        props: { dashboard },
    }
}

const routes = [
    { path: '/:pathMatch(.*)*', name: '404', component: NotFound }
];

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