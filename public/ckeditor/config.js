/**
 * @license Copyright (c) 2003-2020, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';

    config.toolbarGroups = [
        { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
        { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
        { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
        { name: 'forms', groups: [ 'forms' ] },
        { name: 'styles', groups: [ 'styles' ] },
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
        { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
        { name: 'links', groups: [ 'links' ] },
        { name: 'insert', groups: [ 'insert' ] },
        { name: 'colors', groups: [ 'colors' ] },
        { name: 'tools', groups: [ 'tools' ] },
        { name: 'others', groups: [ 'others' ] },
        { name: 'about', groups: [ 'about' ] }
    ];
    config.removeButtons = 'Save,NewPage,Preview,Print,Source,Templates,PasteFromWord,PasteText,Replace,SelectAll,Scayt,TextField,Textarea,HiddenField,Button,Select,Form,CreateDiv,Language,Simplebox,Flash,Smiley,PageBreak,Iframe,Maximize,ShowBlocks,About,Paste,Copy,Cut,Find,Checkbox,Radio,CopyFormatting,RemoveFormat,Subscript,Superscript,BidiRtl,BidiLtr,Anchor,HorizontalRule,Table,Styles,Format';

    config.extraPlugins = [
        'image2',
        'scoreeventhandler',
        'videoupload',
        'autolink'
    ];
    config.extraAllowedContent = [
        'img[*]',
        'figure[*]',
        'figcaption[*]',
        'a[*]'
    ];

};
