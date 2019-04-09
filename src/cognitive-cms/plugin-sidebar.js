( function( wp ) {
    var registerPlugin = wp.plugins.registerPlugin;
    var Fragment = wp.element.Fragment;
    var PluginSidebar = wp.editPost.PluginSidebar;
    var PluginSidebarMoreMenuItem = wp.editPost.PluginSidebarMoreMenuItem;
    var el = wp.element.createElement;
    var Textarea = wp.components.TextareaControl;
    var Checkbox = wp.components.CheckboxControl;
    var withSelect = wp.data.withSelect;
    var withDispatch = wp.data.withDispatch;
    var compose = wp.compose.compose;

    const cognitiveCMSIcon = el('img',
    {
        width: 20,
        height: 20,
        src: 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxOS4xLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHdpZHRoPSI1MHB4IiBoZWlnaHQ9IjUwcHgiIHZpZXdCb3g9IjAgMCA1MCA1MCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgNTAgNTAiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPHBhdGggZmlsbD0iIzAwOTU4MCIgZD0iTTQ5LjM4MywyNC44NzNjLTAuOTA3LTIuMTUtMi43ODUtMy45Ny01LjIxNC01LjA4M2MtMS4xMi0zLjc2NS00LjI1NC03LjUzLTguOTI4LTcuNTMNCgljLTAuMTQ5LDAtMC4yOTksMC4wMDQtMC40NTEsMC4wMTFjLTEuOTQ5LTEuMzk4LTMuODQzLTIuMDc5LTUuNzcxLTIuMDc5Yy0xLjE2MiwwLTIuMzUsMC4yNTctMy41NDEsMC43NjQNCgljLTAuODgyLTAuNDYyLTEuODkzLTAuNzAzLTIuOTc1LTAuNzAzYy0yLjM5MiwwLTQuNjYxLDEuMTA1LTYuMDYxLDIuMzk5Yy0wLjQ0MS0wLjEwNC0wLjktMC4xNTYtMS4zNzQtMC4xNTYNCgljLTMuNTY4LDAtNy4xMDQsMi45NDgtOC4wNiw2LjUyMmMtMy4wNzYsMS4xMTYtNS4zMjIsMy4xNDMtNi4zNzcsNS43ODFjLTEuMDIzLDIuNTU5LTAuNzk4LDUuNDIsMC42MTcsNy44NQ0KCWMxLjQwNiwyLjQxMywzLjg2NiwzLjg1NCw2LjU4LDMuODU0YzEuMDQxLDAsMi4wNDUtMC4yMTMsMi45Ni0wLjYyMmMxLjY1LDEuNSwzLjc3MywyLjM5Nyw1Ljc4NCwyLjM5Nw0KCWMwLjcwMSwwLDEuMzcyLTAuMTA0LDIuMDA3LTAuMzA5YzEuNjI4LDEuMzM5LDQuMTIzLDEuODQxLDYuMTQ4LDEuODQxYzIuMzg5LDAsNC40NjUtMC42MzEsNS44NDEtMS43MzMNCgljMC44OTIsMC40MTMsMS44NzQsMC42MjcsMi44OTksMC42MjdoMC4wMDFjMi41NjMsMCw1LjIxMi0xLjM2Niw2LjY1OS0zLjI2NWMwLjgwNywwLjMzMSwxLjY1NiwwLjUwMiwyLjUxNSwwLjUwMg0KCWMwLDAsMC4wMDEsMCwwLjAwMSwwYzIuODAxLTAuMDAxLDUuNDAyLTEuODI5LDYuNjI2LTQuNjU4QzUwLjIwMywyOS4xMjQsNTAuMjQzLDI2LjkwOCw0OS4zODMsMjQuODczeiIvPg0KPHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTE2LjA3LDM4LjI1NnYtOC44MDRjMC0xLjA5My0wLjg4OS0xLjk4MS0xLjk4MS0xLjk4MUgxMC43OXYxLjRoMy4yOTljMC4zMiwwLDAuNTgxLDAuMjYxLDAuNTgxLDAuNTgxDQoJdjguNTY5QzE1LjEzNCwzOC4xNDQsMTUuNjA0LDM4LjIyMiwxNi4wNywzOC4yNTZ6Ii8+DQo8cGF0aCBmaWxsPSIjRkZGRkZGIiBkPSJNMjIuMTU4LDIzLjE5NmgtNC40MzJjLTAuMzIsMC0wLjU4MS0wLjI2MS0wLjU4MS0wLjU4MVYxMi4wNzZjLTAuMjUyLDAuMTg2LTAuNDg4LDAuMzc4LTAuNzAyLDAuNTc2DQoJYy0wLjIyOC0wLjA1NC0wLjQ2MS0wLjA5My0wLjY5OC0wLjExOXYxMC4wODJjMCwxLjA5MywwLjg4OSwxLjk4MSwxLjk4MSwxLjk4MWg0LjQzMmMwLjMyLDAsMC41OCwwLjI2LDAuNTgsMC41OHYzLjI4MmgxLjR2LTMuMjgyDQoJQzI0LjEzOSwyNC4wODQsMjMuMjUsMjMuMTk2LDIyLjE1OCwyMy4xOTZ6Ii8+DQo8cGF0aCBmaWxsPSIjRkZGRkZGIiBkPSJNNDAuODAzLDI4Ljc5NHYtMS4yNTZoLTEuNDAxdjEuMjU2YzAsMC4zMi0wLjI2LDAuNTgxLTAuNTgsMC41ODFIMzMuMjFWMTkuMzM0DQoJYzAtMS4wOTItMC44ODgtMS45ODEtMS45ODEtMS45ODFoLTMuODE3djEuNGgzLjgxN2MwLjMyLDAsMC41OCwwLjI2LDAuNTgsMC41ODF2MTkuMTcxYzAuNDUzLDAuMTEsMC45MjIsMC4xNzQsMS40LDAuMTkxdi03LjkyDQoJaDUuNjExQzM5LjkxNCwzMC43NzUsNDAuODAzLDI5Ljg4Nyw0MC44MDMsMjguNzk0eiIvPg0KPHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTIzLjQzOSwzNC4yNWMtMS43MiwwLTMuMTItMS40LTMuMTItMy4xMmMwLTEuNzIxLDEuNC0zLjEyMSwzLjEyLTMuMTIxYzEuNzIxLDAsMy4xMiwxLjQsMy4xMiwzLjEyMQ0KCUMyNi41NTksMzIuODUsMjUuMTU5LDM0LjI1LDIzLjQzOSwzNC4yNXogTTIzLjQzOSwyOS4zMDRjLTEuMDA2LDAtMS44MjUsMC44MTktMS44MjUsMS44MjZjMCwxLjAwNywwLjgxOSwxLjgyNiwxLjgyNSwxLjgyNg0KCWMxLjAwNywwLDEuODI1LTAuODE5LDEuODI1LTEuODI2QzI1LjI2NCwzMC4xMjMsMjQuNDQ1LDI5LjMwNCwyMy40MzksMjkuMzA0eiIvPg0KPHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTguNTI1LDMxLjI4Yy0xLjcyMSwwLTMuMTItMS40LTMuMTItMy4xMjFjMC0xLjcyMSwxLjQtMy4xMiwzLjEyLTMuMTJzMy4xMiwxLjQsMy4xMiwzLjEyDQoJQzExLjY0NSwyOS44OCwxMC4yNDYsMzEuMjgsOC41MjUsMzEuMjh6IE04LjUyNSwyNi4zMzNjLTEuMDA3LDAtMS44MjUsMC44MTktMS44MjUsMS44MjZjMCwxLjAwNywwLjgxOSwxLjgyNiwxLjgyNSwxLjgyNg0KCXMxLjgyNS0wLjgxOSwxLjgyNS0xLjgyNkMxMC4zNTEsMjcuMTUyLDkuNTMyLDI2LjMzMyw4LjUyNSwyNi4zMzN6Ii8+DQo8cGF0aCBmaWxsPSIjRkZGRkZGIiBkPSJNNDAuMTE0LDI4LjMwMWMtMS43MjEsMC0zLjEyLTEuNC0zLjEyLTMuMTJjMC0xLjcyMSwxLjQtMy4xMiwzLjEyLTMuMTJjMS43MiwwLDMuMTIsMS40LDMuMTIsMy4xMg0KCUM0My4yMzQsMjYuOTAyLDQxLjgzNCwyOC4zMDEsNDAuMTE0LDI4LjMwMXogTTQwLjExNCwyMy4zNTZjLTEuMDA3LDAtMS44MjUsMC44MTktMS44MjUsMS44MjVzMC44MTksMS44MjYsMS44MjUsMS44MjYNCgljMS4wMDYsMCwxLjgyNS0wLjgxOSwxLjgyNS0xLjgyNlM0MS4xMiwyMy4zNTYsNDAuMTE0LDIzLjM1NnoiLz4NCjxwYXRoIGZpbGw9IiNGRkZGRkYiIGQ9Ik0yNS4xNDcsMjEuMTYxYy0xLjcyMSwwLTMuMTItMS40LTMuMTItMy4xMmMwLTEuNzIxLDEuNC0zLjEyLDMuMTItMy4xMnMzLjEyLDEuNCwzLjEyLDMuMTINCglDMjguMjY4LDE5Ljc2MSwyNi44NjgsMjEuMTYxLDI1LjE0NywyMS4xNjF6IE0yNS4xNDcsMTYuMjE1Yy0xLjAwNywwLTEuODI1LDAuODE5LTEuODI1LDEuODI2YzAsMS4wMDYsMC44MTksMS44MjUsMS44MjUsMS44MjUNCglzMS44MjUtMC44MTksMS44MjUtMS44MjVDMjYuOTczLDE3LjAzNCwyNi4xNTQsMTYuMjE1LDI1LjE0NywxNi4yMTV6Ii8+DQo8L3N2Zz4NCg=='
    });

    var IncludeInCognitiveCmsField = compose(
        withDispatch( function( dispatch, props ) {
            return {
                setMetaFieldValue: function( value ) {
                    dispatch( 'core/editor' ).editPost(
                        { meta: { [ props.fieldName ]: value } }
                    );
                }
            }
        } ),
        withSelect( function( select, props ) {
            return {
                metaFieldValue: select( 'core/editor' )
                    .getEditedPostAttribute( 'meta' )
                    [ props.fieldName ],
            }
        } )
    )( function( props ) {
        return el( Checkbox, {
            label: 'Include in Cognitive CMS?',
            checked: props.metaFieldValue,
            onChange: function( content ) {
                props.setMetaFieldValue( content );
            },
        } );
    } );

    var PersonsField = compose(
        withDispatch( function( dispatch, props ) {
            return {
                setMetaFieldValue: function( value ) {
                    dispatch( 'core/editor' ).editPost(
                        { meta: { [ props.fieldName ]: value } }
                    );
                }
            }
        } ),
        withSelect( function( select, props ) {
            return {
                metaFieldValue: select( 'core/editor' )
                    .getEditedPostAttribute( 'meta' )
                    [ props.fieldName ],
            }
        } )
    )( function( props ) {
        return el( Textarea, {
            label: 'Persons',
            value: props.metaFieldValue,
            onChange: function( content ) {
                props.setMetaFieldValue( content );
            },
        } );
    } );

    var LocationsField = compose(
        withDispatch( function( dispatch, props ) {
            return {
                setMetaFieldValue: function( value ) {
                    dispatch( 'core/editor' ).editPost(
                        { meta: { [ props.fieldName ]: value } }
                    );
                }
            }
        } ),
        withSelect( function( select, props ) {
            return {
                metaFieldValue: select( 'core/editor' )
                    .getEditedPostAttribute( 'meta' )
                    [ props.fieldName ],
            }
        } )
    )( function( props ) {
        return el( Textarea, {
            label: 'Locations',
            value: props.metaFieldValue,
            onChange: function( content ) {
                props.setMetaFieldValue( content );
            },
        } );
    } );

    var OrganizationsField = compose(
        withDispatch( function( dispatch, props ) {
            return {
                setMetaFieldValue: function( value ) {
                    dispatch( 'core/editor' ).editPost(
                        { meta: { [ props.fieldName ]: value } }
                    );
                }
            }
        } ),
        withSelect( function( select, props ) {
            return {
                metaFieldValue: select( 'core/editor' )
                    .getEditedPostAttribute( 'meta' )
                    [ props.fieldName ],
            }
        } )
    )( function( props ) {
        return el( Textarea, {
            label: 'Organizations',
            value: props.metaFieldValue,
            onChange: function( content ) {
                props.setMetaFieldValue( content );
            },
        } );
    } );

    var KeyPhrasesField = compose(
        withDispatch( function( dispatch, props ) {
            return {
                setMetaFieldValue: function( value ) {
                    dispatch( 'core/editor' ).editPost(
                        { meta: { [ props.fieldName ]: value } }
                    );
                }
            }
        } ),
        withSelect( function( select, props ) {
            return {
                metaFieldValue: select( 'core/editor' )
                    .getEditedPostAttribute( 'meta' )
                    [ props.fieldName ],
            }
        } )
    )( function( props ) {
        return el( Textarea, {
            label: 'Key phrases',
            value: props.metaFieldValue,
            onChange: function( content ) {
                props.setMetaFieldValue( content );
            },
        } );
    } );

    registerPlugin( 'asccms-plugin-sidebar', {
        render: function() {
            return el(
                    Fragment,
                    {},
                    el(
                        PluginSidebarMoreMenuItem,
                        {
                            target: 'asccms-plugin-sidebar',
                            icon: cognitiveCMSIcon
                        },
                        'Cognitive CMS'
                    ),
                    el(
                        PluginSidebar,
                        {
                            name: 'asccms-plugin-sidebar',
                            icon: cognitiveCMSIcon,
                            title: 'Cognitive CMS',
                        },
                        el( 'div',
                            { className: 'plugin-sidebar-content' },
                            el( IncludeInCognitiveCmsField,
                                { fieldName: 'asccms_include_in_cognitive_cms' }
                            ),
                            el( PersonsField,
                                { fieldName: 'asccms_persons' }
                            ),
                            el( LocationsField,
                                { fieldName: 'asccms_locations' }
                            ),
                            el( OrganizationsField,
                                { fieldName: 'asccms_organizations' }
                            ),
                            el( KeyPhrasesField,
                                { fieldName: 'asccms_key_phrases' }
                            )
                        )
                    )
                );
        }
    } );
} )( window.wp );