/**
 * @license Copyright (c) 2003-2020, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md.
 */

/* global CKEDITOR */

import { getEditorNamespace } from './utils/geteditornamespace.js';

export default {
    name: 'gtckeditor',

    render( createElement ) {
        return createElement( 'div', {}, [
            createElement( this.tagName )
        ] );
    },

    props: {
        value: {
            type: String,
            default: ''
        },
        type: {
            type: String,
            default: 'classic',
            validator: type => [ 'classic', 'inline' ].includes( type )
        },
        editorUrl: {
            type: String,
            default: 'https://cdn.ckeditor.com/4.14.1/standard-all/ckeditor.js'
        },
        config: {
            type: Object,
            default: () => {}
        },
        tagName: {
            type: String,
            default: 'textarea'
        },
        readOnly: {
            type: Boolean,
            default: null // Use null as the default value, so `config.readOnly` can take precedence.
        },
        disableAutoInline: {
            type: Boolean,
            default: true
        },
        inEdit: {
            type: Boolean,
            default: false
        },
        sectionId: {
            type: Number
        }
    },

    mounted() {
        getEditorNamespace( this.editorUrl ).then( () => {
            if ( this.$_destroyed ) {
                return;
            }

            const config = this.config || {};

            if ( this.readOnly !== null ) {
                config.readOnly = this.readOnly;
            }

            const method = this.type === 'inline' ? 'inline' : 'replace';
            const element = this.$el.firstElementChild;
            CKEDITOR.disableAutoInline= this.disableAutoInline
            const editor = this.instance = CKEDITOR[ method ]( element, config );

            editor.on( 'instanceReady', (ev) => {
                const data = this.value;

                editor.fire( 'lockSnapshot' );

                editor.setData( data, { callback: () => {
                        this.$_setUpEditorEvents();

                        const newData = editor.getData();

                        // Locking the snapshot prevents the 'change' event.
                        // Trigger it manually to update the bound data.
                        if ( data !== newData ) {
                            this.$once( 'input', () => {
                                this.$emit( 'ready', editor );
                            } );

                            this.$emit( 'input', newData );
                        } else {
                            this.$emit( 'ready', editor );
                        }

                        editor.fire( 'unlockSnapshot' );
                    } } );
                this.toggleToolbar()
            } );
        } );
    },

    beforeDestroy() {
        if ( this.instance ) {
            document.getElementsByClassName('content-body')[0].removeEventListener('scroll', this.toolbarResizeTop)
            this.instance.destroy()
        }

        this.$_destroyed = true
    },

    watch: {
        value( val ) {
            if ( this.instance && this.instance.getData() !== val ) {
                this.instance.setData( val );
            }
        },

        readOnly( val ) {
            if ( this.instance ) {
                this.instance.setReadOnly( val );
            }
        },

        inEdit(){
            this.toggleToolbar()
        }
    },

    methods: {
        $_setUpEditorEvents() {
            const editor = this.instance;

            editor.on( 'change', evt => {
                const data = editor.getData();

                // Editor#change event might be fired without an actual data change.
                if ( this.value !== data ) {
                    // The compatibility with the v-model and general Vue.js concept of input–like components.
                    this.$emit( 'input', data, evt, editor );
                }
                // stop the event bubble and resize toolbar
                evt.stop();
                this.toolbarResizeTop();
            } );

            editor.on( 'focus', evt => {
                this.$emit( 'focus', evt, editor );
                this.toolbarResizeTop();
            } );

            editor.on( 'blur', evt => {
                this.$emit( 'blur', evt, editor );
            } );
        },
        toggleToolbar() {
            if(this.inEdit){
                document.getElementById(this.instance.id + '_top').style.cssText = ';display:block!important;margin-top: -35px;';
                document.getElementsByClassName('content-body')[0].addEventListener('scroll', this.toolbarResizeTop)
            }else{
                document.getElementById(this.instance.id + '_top').style.cssText = ';display:none!important;';
            }
        },
        toolbarResizeTop () {
            let sectionId = this.sectionId;
            let instanceId = this.instance.id;

            const contentHeaderHeight = document.getElementsByClassName('content-header')[0].clientHeight
            const top = document.getElementById('Section-' + sectionId).getBoundingClientRect().top

            let instance = document.getElementsByClassName(instanceId);

            if(!((top + 158) < (contentHeaderHeight + 35))) {
                instance[0].style.cssText += ';top: ' + (top + 158) + 'px;'
            } else {
                instance[0].style.cssText += ';top: ' + (contentHeaderHeight + 35) + 'px;'
            }
        }
    }
};
