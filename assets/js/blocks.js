
/**
 * Minecraft Server List Gutenberg Blocks
 */
(function(blocks, element, blockEditor, components, i18n, serverSideRender) {
    var el = element.createElement;
    var __ = i18n.__;
    var registerBlockType = blocks.registerBlockType;
    var InspectorControls = blockEditor.InspectorControls;
    var TextControl = components.TextControl;
    var SelectControl = components.SelectControl;
    var ToggleControl = components.ToggleControl;
    var PanelBody = components.PanelBody;
    var ServerSideRender = serverSideRender;
    
    // Register Server Status Block
    registerBlockType('minecraft-server-list/server-status', {
        title: __('Minecraft Server Status'),
        icon: 'visibility',
        category: 'widgets',
        attributes: {
            ip: {
                type: 'string',
                default: ''
            },
            port: {
                type: 'string',
                default: '25565'
            },
            type: {
                type: 'string',
                default: 'java'
            },
            showPlayers: {
                type: 'boolean',
                default: true
            }
        },
        
        edit: function(props) {
            var attributes = props.attributes;
            
            return [
                el(InspectorControls, { key: 'inspector' },
                    el(PanelBody, { title: __('Server Settings'), initialOpen: true },
                        el(TextControl, {
                            label: __('Server IP'),
                            value: attributes.ip,
                            onChange: function(value) {
                                props.setAttributes({ ip: value });
                            }
                        }),
                        el(TextControl, {
                            label: __('Server Port'),
                            value: attributes.port,
                            onChange: function(value) {
                                props.setAttributes({ port: value });
                            }
                        }),
                        el(SelectControl, {
                            label: __('Server Type'),
                            value: attributes.type,
                            options: [
                                { label: __('Java Edition'), value: 'java' },
                                { label: __('Bedrock Edition'), value: 'bedrock' }
                            ],
                            onChange: function(value) {
                                props.setAttributes({ type: value });
                            }
                        }),
                        el(ToggleControl, {
                            label: __('Show Player Count'),
                            checked: attributes.showPlayers,
                            onChange: function(value) {
                                props.setAttributes({ showPlayers: value });
                            }
                        })
                    )
                ),
                el(ServerSideRender, {
                    block: 'minecraft-server-list/server-status',
                    attributes: attributes
                })
            ];
        },
        
        save: function() {
            return null; // Server-side rendered
        }
    });
    
    // Register Servers List Block
    registerBlockType('minecraft-server-list/servers', {
        title: __('Minecraft Servers List'),
        icon: 'list-view',
        category: 'widgets',
        attributes: {
            limit: {
                type: 'number',
                default: 5
            },
            edition: {
                type: 'string',
                default: ''
            },
            serverType: {
                type: 'string',
                default: ''
            },
            sortBy: {
                type: 'string',
                default: 'rank'
            },
            showFeatured: {
                type: 'boolean',
                default: false
            },
            showOnlineOnly: {
                type: 'boolean',
                default: false
            }
        },
        
        edit: function(props) {
            var attributes = props.attributes;
            
            return [
                el(InspectorControls, { key: 'inspector' },
                    el(PanelBody, { title: __('List Settings'), initialOpen: true },
                        el(TextControl, {
                            label: __('Number of Servers'),
                            type: 'number',
                            value: attributes.limit,
                            onChange: function(value) {
                                props.setAttributes({ limit: parseInt(value) });
                            }
                        }),
                        el(SelectControl, {
                            label: __('Edition'),
                            value: attributes.edition,
                            options: [
                                { label: __('All Editions'), value: '' },
                                { label: __('Java Edition'), value: 'java' },
                                { label: __('Bedrock Edition'), value: 'bedrock' },
                                { label: __('Java & Bedrock'), value: 'java_bedrock' }
                            ],
                            onChange: function(value) {
                                props.setAttributes({ edition: value });
                            }
                        }),
                        el(SelectControl, {
                            label: __('Sort By'),
                            value: attributes.sortBy,
                            options: [
                                { label: __('Rank'), value: 'rank' },
                                { label: __('Most Players'), value: 'players' },
                                { label: __('Most Votes'), value: 'votes' },
                                { label: __('Rating'), value: 'rating' },
                                { label: __('Newest'), value: 'newest' },
                                { label: __('Name (A-Z)'), value: 'name' }
                            ],
                            onChange: function(value) {
                                props.setAttributes({ sortBy: value });
                            }
                        }),
                        el(ToggleControl, {
                            label: __('Show Featured Servers Only'),
                            checked: attributes.showFeatured,
                            onChange: function(value) {
                                props.setAttributes({ showFeatured: value });
                            }
                        }),
                        el(ToggleControl, {
                            label: __('Show Online Servers Only'),
                            checked: attributes.showOnlineOnly,
                            onChange: function(value) {
                                props.setAttributes({ showOnlineOnly: value });
                            }
                        })
                    )
                ),
                el(ServerSideRender, {
                    block: 'minecraft-server-list/servers',
                    attributes: attributes
                })
            ];
        },
        
        save: function() {
            return null; // Server-side rendered
        }
    });
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor,
    window.wp.components,
    window.wp.i18n,
    window.wp.serverSideRender
));
