CKEDITOR.plugins.add( 'scoreeventhandler', {
    init: function( editor ) {

        editor.on( 'paste', function( evt ) {

            if(evt.data.dataValue.indexOf('<img') > -1){
                evt.data.dataValue = evt.data.dataValue.replace(/ ref="(.*?)"/g, '')
            }

            if(evt.data.dataValue.indexOf('<a') > -1){
                evt.data.dataValue = evt.data.dataValue.replace(/data-db-id="(.*?)"/g, '')
            }

        });

        if(!window.eventBus.$data.eventListenerClick) {

            window.eventBus.$data.eventListenerClick = true
            var sections = document.getElementById('sections');
            sections.addEventListener('click', function (e) {

                //make all links possible to visit with one left click
                if(e.target.parentNode.nodeName == 'A' && !e.target.id) {
                    window.open(e.target.parentNode.href);
                }
                if (e.target.nodeName == 'A' && !e.target.id) {
                    window.open(e.target.href);
                }

                // sets active ck editor
                if (e.target) {
                    var ck_instance_name = false;

                    for (var ck_instance in CKEDITOR.instances) {
                        if (CKEDITOR.instances[ck_instance].focusManager.hasFocus) {
                            ck_instance_name = ck_instance;
                            window.eventBus.$data.activeCkEditorId = ck_instance_name
                        }
                    }
                }

                // checks if a figcaption can be changed based on the readonly state of the parent ckeditor
                if(CKEDITOR.instances[window.eventBus.$data.activeCkEditorId] && !CKEDITOR.instances[window.eventBus.$data.activeCkEditorId].readOnly){
                    if(e.target.nodeName == 'FIGCAPTION') {
                        e.target.setAttribute('contenteditable', true)
                        CKEDITOR.instances[window.eventBus.$data.activeCkEditorId].getCommand('insertVideoupload').disable();
                        CKEDITOR.instances[window.eventBus.$data.activeCkEditorId].getCommand('simplelink').disable();
                    }
                }else{
                    if(e.target.nodeName == 'FIGCAPTION') {
                        e.target.setAttribute('contenteditable', false)
                    }
                }

                // sets all figcaptions of videoannotations to not editable
                if (e.target.attributes && e.target.attributes.notEditable) {
                    e.target.setAttribute('contenteditable', false);
                }

            })
        }

        if(!window.eventBus.$data.eventListenerInsertedAnnotation) {
            window.eventBus.$data.eventListenerInsertedAnnotation = true
            window.eventBus.$on('insertedAnnotation', function (data) {
                if (data.showThumbnail) {
                    CKEDITOR.instances[window.eventBus.$data.activeCkEditorId].insertHtml('<figure class="image cke_widget_element"><img id="Annotation'+data.id+''+data.idTag+'" width="150px" src="'+data.showThumbnail+'"><figcaption notEditable="true" contenteditable="true" data-cke-widget-editable="caption" data-cke-enter-mode="2" class="cke_widget_editable"  data-cke-display-name="' + data.timestamp + '">' + data.timestamp + ' - ' + data.body + '</figcaption></figure>')
                } else {
                    CKEDITOR.instances[window.eventBus.$data.activeCkEditorId].insertHtml('<a contenteditable="false" id="Annotation'+data.id+''+data.idTag+'" href="#">' + data.timestamp + ' ' + data.body + '</a>')
                }

            })
        }

        if(!window.eventBus.$data.eventListenerInsertedSequence) {
            window.eventBus.$data.eventListenerInsertedSequence = true
            window.eventBus.$on('insertedSequence', function (data) {
                if(data.thumbnail){
                    CKEDITOR.instances[window.eventBus.$data.activeCkEditorId].insertHtml('<figure class="image cke_widget_element"><img id="Sequence'+data.id+'Media'+data.mediaId+'" width="150px" src="'+data.thumbnail+'"><figcaption notEditable="true" contenteditable="true" data-cke-widget-editable="caption" data-cke-enter-mode="2" class="cke_widget_editable"  data-cke-display-name="' + data.timestamp + '">' + data.timestamp + ' - ' + data.endtimeTimestamp +' - ' + data.title + '</figcaption></figure>')
                } else {
                    CKEDITOR.instances[window.eventBus.$data.activeCkEditorId].insertHtml('<a contenteditable="false" id="Sequence'+data.id+'Media'+data.mediaId+'" href="#">' + data.timestamp + ' - ' + data.endtimeTimestamp +' - ' + data.title + '</a>')
                }
            })
        }

        if(!window.eventBus.$data.eventListenerVideoUploadDone){
            window.eventBus.$data.eventListenerVideoUploadDone = true

            window.eventBus.$on('videoUploadDone', function (payload) {
                window.eventBus.$data.eventListenerVideoUploadDone = true
                        CKEDITOR.instances[window.eventBus.$data.activeCkEditorId].insertHtml('<figure class="image cke_widget_element"><img id="Image' + payload.id + '" width="150px" src="' + payload.previewURL + '"><figcaption contenteditable="true" data-cke-widget-editable="caption" data-cke-enter-mode="2" class="cke_widget_editable" data-cke-display-name="' + payload.caption + '">' + payload.caption + '</figcaption></figure>')
            });
        }
    },
});
