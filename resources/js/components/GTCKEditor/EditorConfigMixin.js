let EditorConfigMixin = {
    data() {
        return {
            isLock: false,
            editorUrl: '../ckeditor/ckeditor.js',
            gtCkeditorReady: false,
            useEventHandler: true,
            useVideoUpload: true,
            wordcount: null,
        }
    },
    computed: {
        editorConfig: function () {
            let extraPlugins = 'score_image, SimpleLink, autolink, fixed'
            if (this.useEventHandler) {
                extraPlugins += ', scoreeventhandler'
            }
            if (this.useVideoUpload) {
                extraPlugins += ', videoupload'
            }
            if (this.wordcount !== null) {
                extraPlugins += ', wordcount, notification'
            }
            return {
                defaultLanguage: 'de',
                language: 'de',
                readOnly: this.isLock,
                toolbarGroups: [
                    {name: 'document', groups: ['mode', 'document', 'doctools']},
                    {name: 'clipboard', groups: ['clipboard', 'undo']},
                    {name: 'editing', groups: ['find', 'selection', 'spellchecker', 'editing']},
                    {name: 'forms', groups: ['forms']},
                    {name: 'styles', groups: ['styles']},
                    {name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
                    {name: 'paragraph', groups: ['list', 'indent', 'align', 'bidi', 'paragraph']},
                    {name: 'links', groups: ['links']},
                    {name: 'insert', groups: ['insert']},
                    {name: 'colors', groups: ['colors']},
                    {name: 'tools', groups: ['tools']},
                    {name: 'others', groups: ['others']},
                    {name: 'about', groups: ['about']}
                ],
                removeButtons: 'Save,NewPage,Preview,Print,Source,Templates,PasteFromWord,PasteText,Replace,SelectAll,Scayt,TextField,Textarea,HiddenField,Button,Select,Form,CreateDiv,Language,Simplebox,Flash,Smiley,PageBreak,Iframe,Maximize,ShowBlocks,About,Paste,Copy,Cut,Find,Checkbox,Radio,CopyFormatting,RemoveFormat,Subscript,Superscript,BidiRtl,BidiLtr,Anchor,HorizontalRule,Styles,Format',
                removePlugins: "image, image2, link",
                extraPlugins: extraPlugins,
                filebrowserUploadUrl: "/rest/file?type=ckeditor_score_image&_token=" + document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                imageUploadUrl: "/rest/file?_token=" + document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                filebrowserUploadMethod: 'form',
                extraAllowedContent: "img[*];" + "figure[*];" + "figcaption[*];" + "a[*];",
                wordcount: this.wordcount
            }
        },
    }
}
export default EditorConfigMixin
