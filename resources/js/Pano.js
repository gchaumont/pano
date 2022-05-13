import {resourceRoutes, dashboardRoutes, appRoutes} from "./router"

export default function Pano(root) {

    this.root = root;

    this.menu = [];
    this.resources = [];

    if (!this.root.startsWith('/') && this.root.length>1) {
        this.root = '/'+this.root;
    }

    this.boot = function(Router) {
        fetch( this.root + '/api/config')
            .then(response => response.json().then(config => {

                this.menu.push(... config.menu)
                this.configureRouter(config.routes, Router)
                this.name = config.name;
                this.path = config.path;
                this.route = config.route;

                this.resources.push(...config.resources);
                this.dashboards = config.dashboards;
                this.apps = config.apps;
            }))
    }


    this.getResource = function(key) {
        return this.resources.find(r => r.key == key)
    }


    this.configureRouter = function(routes, Router) {
        console.log(routes)
        for (var i = routes.length - 1; i >= 0; i--) {
            if (routes[i].type == 'Resources') {
                Router.addRoute(resourceRoutes(routes[i].path, routes[i].keys))
            } else if (routes[i].type == 'Dashboards') {
                Router.addRoute(dashboardRoutes(routes[i].path))
            } else if (routes[i].type == 'App') {
                Router.addRoute(appRoutes(routes[i].path, routes[i].app))
            }            
        }
            Router.replace(Router.currentRoute.value.fullPath)
    }


}
