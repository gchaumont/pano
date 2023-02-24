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
    return [{
        path: resource.path,
        children: [{
            component: ListResource,
            path: '',
            name: resource.route,
            props: { resource: resource.props, uiPath: resource.uiPath }, //  app, root,
            meta: { 
                page: resource.route+'.index',
                name: resource.props.name  +' - '+app.props.name,
            }
        }, {
            component: ShowResource,
            path: resource.path + '/:record',
            props: route => ({ resource: resource.props, record: route.params.record, uiPath: resource.uiPath, }),
            meta: {
                page: resource.route+'.show',
                name: resource.props.name  +' - '+app.props.name,

            }

        }, {
            component: EditResource,
            path: resource.path + '/:record/edit',
            props: route => ({ app, root, resource, record: route.params.record }),

        }, {
            component: CreateResource,
            path: resource.path + '/create',
            props: { app, root, resource },
        }],
    }]
}

export function appRoutes(app, root) {
    return [{
            component: AppHome,
            path: app.path,
            redirect: app.props.homepage,
            props: { app, root },
            meta: { 
                page: app.name,
                name: app.props.name +' - '+app.props.name,
            },
            children: [
                ...app.props.dashboards.map(dash => dashboardRoute(dash, app, root)),
                ...app.props.resources.map(dash => resourceRoutes(dash, app, root)).flat()
            ]
        },
        ...app.props.apps.map(child => appRoutes(child, root)).flat()
    ]
}

export function dashboardRoute(dashboard, app, root) {
    return {
        component: Dashboard,
        path: dashboard.path,
        name: dashboard.route,
            meta: {
             page: app.name,
            name: dashboard.props.name  +' - '+app.props.name,

         },

        props: () => {
            // console.log(dashboard.props);
            return { dashboard: dashboard.props }
        }
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