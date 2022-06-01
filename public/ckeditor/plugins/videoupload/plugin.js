CKEDITOR.plugins.add( 'videoupload', {
    icons: 'videoupload',
    init: function( editor ) {
        editor.addCommand( 'insertVideoupload', {
            exec: function( editor ) {
                window.eventBus.$emit('triggerVideoUpload')
            }
        });


        editor.ui.addButton( 'videoupload', {
            label: 'Video hochladen',
            command: 'insertVideoupload',
            toolbar: 'insert'
        });
    }
});
