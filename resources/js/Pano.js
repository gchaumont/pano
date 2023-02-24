import { reactive, provide, computed, ref } from 'vue'
import { resourceRoutes, appRoutes } from "./router"
import Wrapper from '@/components/support/Wrapper.vue'
import {useRoute} from 'vue-router'

function wrapRoute(page) {
    // return {
    //     name: page.name,
    //     path: page.path,
    //     alias: page.alias || [],
    //     component: Wrapper,
    //     props: { component: page.component, props: page.props },
    //     children: page.children?.map(child => wrapRoute(child)),
    //     meta: page.meta,
    //     redirect: page.redirect
    // }
    var record = Object.assign(page, {
        props: { component: page.component, props: page.props },
        children: page.children?.map(child => wrapRoute(child)),
        component: Wrapper,
    });

    return record
}


const rootApp = ref(null);
const currentApp = ref(null);

const pano = reactive(new Pano(window.panoConfig));

function setPageTitle(title) {
    document.title = title;
}

export function usePano() {
    return pano
}

export function setRootApp(App) {
    rootApp.value = App;
}
export function setCurrentApp(App) {
    currentApp.value = App;
}

export function useData() {
    const route = useRoute();

    function query({uiPath, endpoint, params = {}}) {
        const path = route.matched.filter(r => r.meta?.page).at(-1).path;
        params = ref(params)
        params.value.uiPath = uiPath;
        params.value.endpoint = endpoint;

        // console.log(id, path, params, new URLSearchParams(params).toString())
         return fetch(path + "?" + new URLSearchParams(params.value), { headers: { 'Accept': 'application/json' } })
            .then(response => response.json())
    }

    return { query }
}


export function resolveProps(props) {
    var route = useRoute()

    // resolve the data and possible endpoints from props and bind it to the component

    if (props) {
        for (var prop in props) {
                var currentProp = props[prop];
            if (currentProp.hasOwnProperty('@type')) {
                if (currentProp['@type'] == 'QueryParameter') {
                    props[currentProp['name']] =  route.params[currentProp['parameter']]
                }
            }
        }
    }

    return props;

}

export const getRootApp = computed(() => rootApp.value)
export const getCurrentApp = computed(() => currentApp.value)

export default function Pano(config) {

    this.config = reactive(config);

    this.useRouter = Router => {
        for (var i = this.config.length - 1; i >= 0; i--) {
            this.registerConfig(this.config[i], Router);
            // Router.addRoute(wrapRoute(config[i]))
        }

        Router.beforeEach((to, from) => {
            if (to.meta.name) {
                setPageTitle(to.meta.name)
                // setRootApp(to.meta.rootApp)
                // setCurrentApp(to.meta.app)
            }
            return true
        })
    }


    this.registerConfig = (config, Router) => {
        if (config['@type'] == 'Application') {
            this.registerApplication(config, Router)
        } else if (config['@type'] == 'Page') {
            this.registerPage(config, Router)
        }
    }


    this.registerApplication = (config, Router, root = null) => {
        // console.log(config);
        if (root == null) {
            root = config;
        }



        appRoutes(config, root).forEach(route => Router.addRoute(route))
        

        // for (var i = config.dashboards.length - 1; i >= 0; i--) {
        //     Router.addRoute(dashboardRoutes(config.dashboards[i], config, root));
        // }

        // for (var i = config.resources.length - 1; i >= 0; i--) {
        //     Router.addRoute(resourceRoutes(config.resources[i], config, root));
        // }

        // for (var i = config.apps.length - 1; i >= 0; i--) {
        //     this.registerApplication(config.apps[i], Router, root);
        // }
    }

    this.registerPage = (config, Router) => {

    }


    // this.menu = [];
    // this.resources = [];


    // this.boot = Router => {
    //     this.menu.push(...config.menu)
    //     this.name = config.name;
    //     this.path = config.path;
    //     this.route = config.route;

    //     this.resources = config.resources;
    //     this.dashboards = config.dashboards;
    //     this.apps = config.apps;

    //     this.registerAppRoutes(config, Router)
    // }


    // this.getResource = (key) => this.resources.find(r => r.key == key)
}