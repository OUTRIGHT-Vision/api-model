module.exports = {
    title: 'OUTRIGHTVision Api Models',
    description: 'Just like Laravel Eloquent but from Api Calls',
    themeConfig: {
        navbar: true,
        sidebar: [{
            title: 'Getting started',
            collapsable: false,
            children: [
                ['/installation', 'Installation'],
            ],
        },{
            title: 'First steps',
            collapsable: false,
            children: [
                ['/how_to_use', 'Creating your first model'],
                ['/relationships', 'Relationships'],
            ],
        },{
            title: 'Advanced',
            collapsable: false,
            children: [
                ['/advanced', 'Advanced stuff'],
            ],
        }]
    },
}
