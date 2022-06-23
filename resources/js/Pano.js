import { resourceRoutes, dashboardRoutes, appRoutes } from "./router"

export default function Pano(root) {

    this.root = root;

    this.menu = [];
    this.resources = [];

    this.rootApp ;

    if (!this.root.startsWith('/') && this.root.length > 1) {
        this.root = '/' + this.root;
    }

    this.boot = function(Router) {
        fetch(this.root + '/api/config')
            .then(response => response.json().then(config => {

                this.rootApp = config;
                this.menu.push(...config.menu)
                this.registerAppRoutes(config, Router)
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


    this.registerAppRoutes = function(config, Router, parent) {

        Router.addRoute(appRoutes(config, parent))

        for (var i = config.dashboards.length - 1; i >= 0; i--) {
            Router.addRoute(dashboardRoutes(config.dashboards[i], config, parent));
        }

        for (var i = config.resources.length - 1; i >= 0; i--) {
            Router.addRoute(resourceRoutes(config.resources[i], config, parent));
        }

        for (var i = config.apps.length - 1; i >= 0; i--) {
            this.registerAppRoutes(config.apps[i], Router, config);
        }

        Router.replace(Router.currentRoute.value.fullPath)
    }



}