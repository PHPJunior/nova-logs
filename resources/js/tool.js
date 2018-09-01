Nova.booting((Vue, router) => {
    router.addRoutes([
        {
            name: 'nova-log-viewer-dashboard',
            path: '/nova-log-viewer/dashboard',
            component: require('./components/Dashboard'),
        },
        {
            name: 'nova-log-viewer-list',
            path: '/nova-log-viewer/list',
            component: require('./components/Logs/LogsTool'),
        },
        {
            name: 'nova-log-viewer-show',
            path: '/nova-log-viewer/list/:date/:level',
            component: require('./components/Show/Logs'),
        },
    ])
})
