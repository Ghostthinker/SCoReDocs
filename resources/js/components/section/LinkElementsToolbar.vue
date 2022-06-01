<template>
    <div>
        <div  id="linkElementToolbar" :style="{left: imageMenuBox.left + 15 + 'px', top: imageMenuBox.top + 15 +'px'}"
              class="ckeditor-image-menu-box">
            <v-tooltip bottom open-delay="500" v-if="videoId">
                <template v-slot:activator="{ on }">
                    <div v-on="on">
                        <v-btn tile depressed
                               class=""
                               disabled>
                            <v-icon class="">mdi-comment</v-icon>
                            <v-badge :content="''+annotationCount+''" color="primary">

                            </v-badge>
                        </v-btn>
                    </div>
                </template>
                <span>Anzahl der Videokommentare</span>
            </v-tooltip>
            <v-tooltip bottom open-delay="500">
                <template v-slot:activator="{ on }">
                    <div v-on="on">
                        <v-btn tile depressed
                               class=""
                               disabled>
                            <v-icon class="">mdi-link-variant</v-icon>
                            <v-badge :content="''+linkCount+''" color="primary">

                            </v-badge>
                        </v-btn>
                    </div>
                </template>
                <span>Anzahl der Verlinkungen</span>
            </v-tooltip>
            <v-tooltip bottom open-delay="500">
                <template v-slot:activator="{ on }">
                    <div v-on="on">
                        <v-btn tile depressed
                               class=""
                               @click="generateLink"
                               :disabled="disabledToolbar">
                            <v-icon class="">mdi-link</v-icon>
                        </v-btn>
                    </div>
                </template>
                <span>Link in die Zwischenablage kopieren</span>
            </v-tooltip>
            <v-tooltip bottom open-delay="500">
                <template v-slot:activator="{ on }">
                    <div v-on="on">
                        <v-btn tile depressed
                               class=""
                               @click="generateLinkByDialog"
                               :disabled="disabledToolbar">
                            <v-icon class="">mdi-link-plus</v-icon>
                        </v-btn>
                    </div>
                </template>
                <span>Link mit Anzeigetext erstellen</span>
            </v-tooltip>
            <template v-if="isEvoli">
                <v-tooltip bottom open-delay="500" v-if="videoId && !sequenceId && canDownloadMedia">
                    <template v-slot:activator="{ on }">
                        <div v-on="on">
                            <v-btn tile depressed
                                   class=""
                                   @click="downloadMedia"
                                   :disabled="disabledToolbar">
                                <v-icon class="">mdi-download</v-icon>
                            </v-btn>
                        </div>
                    </template>
                    <span>Video herunterladen</span>
                </v-tooltip>
                <v-tooltip bottom open-delay="500" v-if="videoId && sequenceId">
                    <template v-slot:activator="{ on }">
                        <div v-on="on">
                            <v-btn tile depressed
                                   class=""
                                   @click="downloadSequence"
                                   :disabled="disabledToolbar">
                                <v-icon class="">mdi-download</v-icon>
                            </v-btn>
                        </div>
                    </template>
                    <span>Sequenz herunterladen</span>
                </v-tooltip>
            </template>
        </div>
        <v-form
            ref="dialogForm"
            v-model="valid"
            lazy-validation
        >
            <v-row justifiy="center">
                <custom-dialog :dialog="showDialog" @close="closeDialog">
                    <template v-slot:title>
                        Link erstellen
                    </template>
                    <template v-slot:content>
                        <v-alert
                            :value="alert"
                            type="error"
                            transition="scale-transition"
                        >
                            Link-Text ist ein Pflichtfeld
                        </v-alert>
                        <v-card-text>
                            <v-text-field v-model="link.label"
                                          label="Link-Text hinzufügen"
                                          :rules="linkLabelRules"
                                          required
                            ></v-text-field>
                        </v-card-text>
                    </template>
                    <template v-slot:actions>
                        <v-btn @click="createHtmlLink">In die Zwischenablage kopieren</v-btn>
                    </template>
                </custom-dialog>
            </v-row>
        </v-form>
    </div>
</template>

<script>
import Link from "../../models/link"
import axios from "axios"
import CustomDialog from "../dialog/CustomDialog"

export default {
    name: "LinkElementsToolbar",
    components: {CustomDialog},
    props: {
        ckEditorImageMenuBox: null
    },
    data() {
        return {
            showDialog: false,
            link: new Link(),
            valid: false,
            alert: false,
            linkLabelRules: [
                v => !!v || 'Link-Text ist ein Pflichtfeld'
            ],
            imageMenuBox: this.ckEditorImageMenuBox,
            count: '0',
            videoId: false,
            sequenceId: '',
            annotationCount: '...',
            linkCount: '...'
        }
    },
    computed: {
        canDownloadMedia() {
            let project = this.$store.getters['projects/getProject']
            return (project && project.user_can_download_media)
        },
        disabledToolbar() {
            return !(this.imageMenuBox.ref != null)
        },
        isEvoli() {
            return window.isEvoli
        }
    },
    methods: {
        downloadMedia() {
            window.open(window.location.origin + '/rest/media/' + this.videoId + '/download/uncompressed')
        },
        downloadSequence() {
            axios.get('/rest/ep5/sequences/' + this.videoId + '/sequence/' + this.sequenceId + '/download').then(function (response) {
                switch (response.data.status) {
                case 200:
                    window.open(response.data.url)
                    break
                case 202:
                    this.$store.dispatch('notifications/persistentNotification', {
                        message: 'Sequenz wird für den Download vorbereitet',
                        type: 'success',
                        isDownload: true,
                        sequenceId: this.sequenceId,
                        videoId: this.videoId,
                        downloadUrl: response.data.url,
                        name: this.sequenceId
                    })
                    break
                default:
                    this.$store.dispatch('notifications/persistentNotification', {
                        message: 'Beim herunterladen der Sequenz oder deren Vorbereitung ist ein Fehler aufgetreten.' +
                            'Bitte versuchen sie es erneut. Sollte der Fehler weierhin auftreten kontaktieren' +
                            'Sie den Support.',
                        type: 'error'
                    })
                }
            }.bind(this))
        },
        generateLink() {
            let link = this.createLink()
            let self = this
            this.copyStringToClipboard(link).then(function () {
                self.$store.dispatch('notifications/newNotification', {
                    message: 'Link wurde in die Zwischenablage kopiert',
                    type: 'success'
                })
            }, function () {
                self.$store.dispatch('notifications/newNotification', {
                    message: 'Link konnte nicht in die Zwischenablage kopiert werden',
                    type: 'error'
                })
            })
        },
        generateLinkByDialog() {
            this.showDialog = true
        },
        createLink() {
            return window.location.origin + window.location.pathname + '#' + this.ckEditorImageMenuBox.ref
        },
        createHtmlLink() {
            this.validateDialog()
            if (!this.valid || this.link.label.length < 1) {
                this.alert = true
                return
            }

            let targetLink = this.createLink()
            let htmlTargetLink = '<a href="' + targetLink + '" id="scoreLink" contenteditable="false">' + this.link.label + '</a>'

            this.copyHtmlStringToClipboard(htmlTargetLink)

            this.$store.dispatch('notifications/newNotification', {
                message: 'Link mit Anzeigetext wurde in die Zwischenablage kopiert',
                type: 'success'
            })

            this.closeDialog()
        },
        closeDialog() {
            this.showDialog = false
            this.link = new Link()
            this.alert = false
        },
        copyStringToClipboard(string) {
            return navigator.clipboard.writeText(string).then(function () {

            }, function () {

            })
        },
        copyHtmlStringToClipboard(linkHtml) {
            //https://stackoverflow.com/questions/34191780/javascript-copy-string-to-clipboard-as-text-html

            // Create container for the HTML
            // [1]
            var container = document.createElement('div')
            container.innerHTML = linkHtml

            // Hide element
            // [2]
            container.style.position = 'fixed'
            container.style.pointerEvents = 'none'
            container.style.opacity = 0

            // Detect all style sheets of the page
            let activeSheets = Array.prototype.slice.call(document.styleSheets)
                .filter(function (sheet) {
                    return !sheet.disabled
                })

            // Mount the container to the DOM to make `contentWindow` available
            // [3]
            document.body.appendChild(container)

            // Copy to clipboard
            // [4]
            window.getSelection().removeAllRanges()

            let range = document.createRange()
            range.selectNode(container)
            window.getSelection().addRange(range)

            // [5.1]
            document.execCommand('copy')

            // [5.2]
            for (let i = 0; i < activeSheets.length; i++) activeSheets[i].disabled = true

            // [5.3]
            document.execCommand('copy')

            // [5.4]
            for (let i = 0; i < activeSheets.length; i++) activeSheets[i].disabled = false

            // Remove the container
            // [6]
            document.body.removeChild(container)
        },
        validateDialog() {
            this.$refs.dialogForm.validate()
        }
    },
    created() {
        if (this.imageMenuBox.id && this.imageMenuBox.id.startsWith('Image')) {
            let preString = 'Image'
            this.videoId = this.imageMenuBox.id.replace(preString, '')

            axios.get('/rest/media/' + this.videoId + '/annotations/count').then((response) => {
                this.annotationCount = response.data
            })
        }

        if (this.imageMenuBox.id && this.imageMenuBox.id.startsWith('Sequence')) {

            let preStringSequence = 'Sequence'
            let preStringMedia = 'Media'

            let payload = this.imageMenuBox.id

            let tempString = payload.split(preStringMedia)

            this.sequenceId = tempString[0].replace(preStringSequence, '')
            this.videoId = tempString[1]

            axios.get('/rest/media/' + this.videoId + '/sequence/' + this.sequenceId + '/count').then((response) => {
                this.annotationCount = response.data
            })
        }

        this.$store.dispatch('sections/fetchElementLinkCount', this.imageMenuBox.ref).then((value) => {
            this.linkCount = value
        })


    }
}
</script>

<style scoped>
.ckeditor-image-menu-box {
  position: fixed;
  background-color: white;
  z-index: 9;
}
</style>
