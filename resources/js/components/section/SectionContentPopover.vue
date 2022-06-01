<template>
    <custom-dialog :dialog="show" :show-close-button="false" @close="close">
        <template v-slot:title>
            Abschnitt "{{ sectionTitle }}" wurde automatisch freigegeben - Nicht gespeicherter Inhalt
        </template>
        <template v-slot:content>
            <gtckeditor class="ckeditor content-view"
                        ref="ckeditor-content-view"
                        type="inline"
                        v-model="content"
                        :config="editorConfig"
                        :read-only="true"
            >
            </gtckeditor>
        </template>
        <template v-slot:actions>
            <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                    <div
                        v-on="on"
                        v-bind="attrs"
                    >
                        <v-btn
                            color="primary"
                            text
                            @click="close"
                        >
                            Schließen
                        </v-btn>
                    </div>
                </template>
                <span>Änderungen verwerfen</span>
            </v-tooltip>
            <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                    <div
                        v-on="on"
                        v-bind="attrs"
                    >
                        <v-btn
                            color="primary"
                            @click="restore"
                            v-show="false"
                        >
                            Wiederherstellen
                        </v-btn>
                    </div>
                </template>
                <span>Änderungen wieder herstellen (geht nur wenn Abschnitt zwischenzeitlich nicht gesperrt wurde)</span>
            </v-tooltip>
        </template>
    </custom-dialog>
</template>

<script>
import CustomDialog from "../dialog/CustomDialog"
export default {
    name: "SectionContentPopover",
    components: {CustomDialog},
    data() {
        return {
            editorConfig:  {
                readOnly: true,
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
                extraAllowedContent: "img[*];" + "figure[*];" + "figcaption[*];" + "a[*];"
            },
            dialog: false
        }
    },
    props: {
        contentData: {
            required: true,
            type: String
        },
        sectionTitle: {
            required: true,
            type: String
        },
        value: Boolean
    },
    methods: {
        close() {
            this.show = false
        },
        restore() {

        }
    },
    computed: {
        show: {
            get () {
                return this.value
            },
            set (value) {
                this.$emit('input', value)
            }
        },
        content: {
            get () {
                return this.contentData
            },
            set () {
                console.error('Can\'t assign new value to content - this prop is readonly.')
            }
        },
    }
}
</script>

<style scoped>

</style>
