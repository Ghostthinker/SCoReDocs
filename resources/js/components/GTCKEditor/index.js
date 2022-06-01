/**
 * @license Copyright (c) 2003-2020, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md.
 */

import GTCKEditorComponent from './GTCKEditor';

const GTCKEditor = {
    /**
     * Installs the plugin, registering the `<gtckeditor>` component.
     *
     * @param {Vue} Vue The Vue object.
     */

    install( Vue ) {
        Vue.component( 'gtckeditor', GTCKEditorComponent );
    },
    component: GTCKEditorComponent
};

export default GTCKEditor;
