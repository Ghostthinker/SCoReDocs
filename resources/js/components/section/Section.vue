<template>
    <v-expansion-panels
        :value="section.isCollapse"
        :readonly="true"
        class="section-expansion-panels"
    >
        <v-expansion-panel>
            <v-main :id="sectionUrl">
                <section-form
                    :inputData.sync="showModal"
                    :section="section"
                    :editorData="editorData"
                    :url="url"
                    :additionalChangeLog="additionalChangeLog"
                    @saved="onSave"
                >
                </section-form>
                <section-delete-log
                    :showDialog.sync="showDialogDelete"
                    :section="section"
                    :url="url"
                >
                </section-delete-log>
                <section-toolbar
                    class="section-toolbar"
                    :section="section"
                    :inEdit=inEdit
                    :isLock="isLock"
                    :hasChanges="hasChanges"
                    :canDelete="canDelete"
                    v-on:saved="onInitSave"
                    v-on:delete="onDeleteSection"
                    v-on:reseted="onResetSection"
                    v-on:edit="onEditSection"
                    v-on:generate-link="onGenerateLink"
                    v-on:generate-link-by-dialog="onGenerateLinkByDialog"
                    v-on:open-reset-dialog="openResetDialog"
                    v-on:start-playlist="$emit('start-playlist', $event)"
                    @show-audits="showAuditsList"
                    @open-section="openSection"
                    @close-section="closeSection"
                >
                </section-toolbar>
                <v-row
                    no-gutters
                    @click="changeUrl"
                >
                    <v-col :cols="12" md="12">
                        <div class="section-wrapper p-2 my-2">
                            <v-expansion-panel-header expand-icon="">
                                <section-header
                                    :inEdit="inEdit"
                                    :canEditTitle="canEditContent"
                                    :canEditHeadingType="canEditHeadingType"
                                    :canEditStatus="canEditStatus"
                                    :section="section"
                                ></section-header>
                            </v-expansion-panel-header>
                            <v-expansion-panel-content>
                                <v-row
                                    no-gutters
                                >
                                    <v-col v-if="inEdit" :cols="12" md="12" :class="isLock && !inEdit ? 'bg-gray': ''">
                                        <gtckeditor class="ckeditor"
                                                    ref="ckeditor"
                                                    type="inline"
                                                    v-model="editorData"
                                                    :config="editorConfig"
                                                    :editor-url="editorUrl"
                                                    :read-only="!canEditContent"
                                                    :inEdit="inEdit"
                                                    :section-id="section.id"
                                                    @input="throttleDialogRefresh(); throttleServerRefresh()"
                                                    @ready="gtCkeditorReady = true; $emit('ckEditorReady')"
                                                    v-view="viewHandler"
                                        >
                                        </gtckeditor>
                                    </v-col>
                                    <v-col v-else :cols="12" md="12" :class="isLock && !inEdit ? 'bg-gray': ''"
                                           v-html="editorData"></v-col>
                                </v-row>
                            </v-expansion-panel-content>
                            <add-section-line :top-section-id="section.id"
                                              v-if="section.addSectionPossible"
                            ></add-section-line>
                        </div>
                    </v-col>
                </v-row>
                <section-chat :section-id="section.id" :section-title="section.title" :project-id="projectId"
                              class="section-chat"></section-chat>
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
                                <v-btn color="primary" @click="createHtmlLink">
                                    In Zwischenablage kopieren
                                </v-btn>
                            </template>
                        </custom-dialog>
                    </v-row>
                </v-form>
                <audit-list
                    :section="section"
                    v-if="showAudits"
                    :show="showAudits"
                    :isLockbyOtherUser="isLock && !inEdit"
                    @close="closeAuditsList"
                    @revert-to-content="revertToContent"
                ></audit-list>
                <section-timeout
                    v-if="showTimeoutDialog"
                    v-model="showTimeoutDialog"
                    :section-id="section.id"
                    :section-title="section.title"
                    :project-id="projectId"
                    @close="showTimeoutDialog = false; refreshTimeout()"
                    @reset="onTimeoutReset"
                >
                </section-timeout>
                <section-content-popover
                    v-model="showLostContentDialog"
                    :content-data="resetContent"
                    :section-title="section.title"
                >
                </section-content-popover>
            </v-main>
        </v-expansion-panel>
    </v-expansion-panels>
</template>

<script>
import SectionToolbar from "./SectionToolbar"
import AddSectionLine from "./AddSectionLine"
import GTCKEditor from '../GTCKEditor'
import axios from "axios"
import Vue from "vue"
import SectionHeader from "./SectionHeader"
import SectionForm from "./SectionForm"
import SectionChat from "./SectionChat"
import Link from "../../models/link"
import AuditList from "../audit/AuditList"
import Section from "../../models/section"
import SectionTimeout from "./SectionTimeout"
import _ from 'lodash'
import SectionContentPopover from "./SectionContentPopover"
import SectionDeleteLog from "./SectionDeleteLog"
import CustomDialog from "../dialog/CustomDialog"

Vue.use(GTCKEditor)

export default {
    name: "SectionView",
    components: {
        CustomDialog,
        SectionContentPopover,
        SectionTimeout,
        AuditList,
        SectionChat,
        SectionHeader,
        AddSectionLine,
        SectionToolbar,
        SectionForm,
        SectionDeleteLog,
    },
    data() {
        return {
            editorUrl: '../ckeditor/ckeditor.js',
            showModal: false,
            showDialog: false,
            showDialogDelete: false,
            links: [],
            link: new Link(),
            linkLabelRules: [
                v => !!v || 'Link-Text ist ein Pflichtfeld'
            ],
            valid: false,
            alert: false,
            showAudits: false,
            showDiff: false,
            additionalChangeLog: '',
            viewedSection: false,
            gtCkeditorReady: false,
            viewHandlerEntered: false,
            inactiveTimeout: null,
            showTimeoutDialog: false,
            initialInput: true,
            showLostContentDialog: false,
            resetContent: '',
            mouseOverElement: null,
            CKEClickEventsRegistered: false,
            isCollapse: 1
        }
    },
    props: {
        section: Section,
        projectId: {
            type: [String, Number]
        },
    },
    computed: {
        sectionUrl() {
            return 'Section-' + this.section.id
        },
        canEditContent() {
            if (this.inEdit === false) {
                return false
            }
            return this.section.userIsEntitledToChangeContent
        },
        canEditHeadingType() {
            return this.section.userIsEntitledToChangeHeadingType
        },
        canEditStatus() {
            return this.section.userIsEntitledToChangeStatus
        },
        canDelete() {
            return this.section.userCanDelete
        },
        hasChanges() {
            if (!(this.editorData === this.section.oldContent))
                if (this.section.oldContent.replace(/\r\n|\n|\r/gm, '') !== this.editorData.replace(/\r\n|\n|\r/gm, ''))
                    return true
            if (!(this.section.title === this.section.oldTitle))
                return true
            if (!(this.section.heading === this.section.oldHeading))
                return true
            return !(this.section.status === this.section.oldStatus)
        },
        editorConfig: function () {
            return {
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
                ...window.isEvoli && {
                    extraPlugins: "score_image, videoupload, scoreeventhandler, SimpleLink, autolink, fixed",
                },
                ...!window.isEvoli && {
                    extraPlugins: "score_image, scoreeventhandler, SimpleLink, autolink, fixed",
                },
                filebrowserUploadUrl: "/rest/file?type=ckeditor_score_image&_token=" + document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                imageUploadUrl: "/rest/file?_token=" + document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                filebrowserUploadMethod: 'form',
                extraAllowedContent: "img[*];" + "figure[*];" + "figcaption[*];" + "a[*]{*};"
            }
        },
        url: function () {
            return '/rest/projects/' + this.projectId + '/sections/' + this.section.id
        },
        editorData: {
            get: function () {
                return this.section.content
            },
            set: function (content) {
                this.section.content = content
            }
        },
        inEdit: {
            get: function () {
                return this.section.lockedByMe === true
            },
            set: function (content) {
                this.section.lockedByMe = content
            }
        },
        isLock: {
            get: function () {
                return this.section.locked === true
            },
            set: function (content) {
                this.section.locked = content
            }
        },
    },
    methods: {
        changeUrl() {
            if (this.$route.hash !== '#' + this.sectionUrl + '') {
                this.$router.replace({hash: '#' + this.sectionUrl + ''})
            }
        },
        onInitSave() {
            if (typeof this.$refs.ckeditor !== "undefined") {
                this.$refs.ckeditor.instance.focusManager.blur()
            }
            this.showModal = true
        },
        onSave() {
            this.section.locked = false
            this.section.oldContent = this.editorData
            this.section.oldStatus = this.section.status
            this.inEdit = false
            this.isLock = false
            if (this.$refs.ckeditor) {
                this.$refs.ckeditor.instance.focusManager.blur()
            }
            this.additionalChangeLog = ''
            this.clearInactiveTimeout()
        },
        onDeleteSection() {
            if (typeof this.$refs.ckeditor !== "undefined") {
                this.$refs.ckeditor.instance.focusManager.blur()
            }
            this.showDialogDelete = true
        },
        onResetSection() {
            this.editorData = this.section.oldContent
            this.section.title = this.section.oldTitle
            this.section.heading = this.section.oldHeading
            this.section.status = this.section.oldStatus
            this.section.statusText = this.section.oldStatusText
            axios.patch(this.url + '/unlock')
            this.inEdit = false
            this.isLock = false
            this.$refs.ckeditor.instance.focusManager.blur()
            this.sendXapi('/canceledediting')
            this.clearInactiveTimeout()
        },
        onEditSection() {
            if (this.isLock && !this.inEdit) {
                throw new Error('Dieser Abschnitt wird bereits bearbeitet')
            }
            if (!this.isLock) {
                axios.patch(this.url + '/lock')
                this.startInactiveTimeout()
            }
            this.inEdit = true
            this.isLock = true
            if (this.inEdit) {
                this.sendXapi('/startedediting')
            }
        },
        onGenerateLink() {
            let link = this.createLink()
            let self = this
            this.copyStringToClipboard(link).then(function () {
                self.$store.dispatch('notifications/newNotification', {
                    message: 'Link zum Abschnitt wurde in die Zwischenablage kopiert',
                    type: 'success'
                })
            }, function () {
                self.$store.dispatch('notifications/newNotification', {
                    message: 'Link zum Abschnitt konnte nicht in die Zwischenablage kopiert werden',
                    type: 'error'
                })
            })
        },
        onGenerateLinkByDialog() {
            if (typeof this.$refs.ckeditor !== "undefined") {
                this.$refs.ckeditor.instance.focusManager.blur()
            }
            this.showDialog = !this.showDialog
        },
        openResetDialog() {
            if (typeof this.$refs.ckeditor !== "undefined") {
                this.$refs.ckeditor.instance.focusManager.blur()
            }
        },
        createLink() {
            return window.location.origin + window.location.pathname + '#' + this.sectionUrl
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
                message: 'Link zum Abschnitt mit Anzeigetext wurde in die Zwischenablage kopiert',
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
        },
        showAuditsList() {
            if (typeof this.$refs.ckeditor !== "undefined") {
                this.$refs.ckeditor.instance.focusManager.blur()
            }
            this.showAudits = true
            this.sendXapi('/viewedhistory')
        },
        closeAuditsList() {
            this.showAudits = false
        },
        revertToContent(audit) {
            try {
                this.onEditSection()
            } catch (e) {
                this.$store.dispatch('notifications/newNotification', {
                    message: 'Der Abschnitt kann derzeit nicht zurück gesetzt werden: ' + e.message,
                    type: 'error'
                })
            }
            this.editorData = audit.state.content
            this.$refs.ckeditor.instance.setData(audit.state.content)
            this.additionalChangeLog =
                'Version von ' + this.section.title + ' wurde auf Version ' + audit.id + ' zurückgesetzt.'
            this.section.title = audit.state.title
            this.sendXapi('/revertedversion/' + audit.id)
        },
        checkIfSectionIsInViewport() {
            let bounding = document.getElementById(this.sectionUrl).getBoundingClientRect()

            return (
                bounding.top >= 0 &&
                bounding.left >= 0 &&
                (bounding.bottom - 50) <= (window.innerHeight || document.documentElement.clientHeight) &&
                bounding.right <= (window.innerWidth || document.documentElement.clientWidth)
            )
        },
        viewHandler(e) {
            if (this.gtCkeditorReady && !this.viewHandlerEntered) {
                if (e.type === 'enter') {
                    this.viewedSection = true
                    this.checkViewedSection()
                }
                if (e.type === 'exit') {
                    this.viewedSection = false
                }
            }
        },
        checkViewedSection() {
            setTimeout(function () {
                if (this.viewedSection) {
                    this.sendViewXapi()
                }
            }.bind(this), 5000)
        },
        onTimeoutReset() {
            this.resetContent = this.editorData
            this.showLostContentDialog = true
            this.onResetSection()
        },
        refreshTimeout() {
            console.log('Refresh inactive timeout')
            this.clearInactiveTimeout()
            this.startInactiveTimeout()
        },
        startInactiveTimeout() {
            console.log('Inactive Timeout set')
            this.inactiveTimeout = setTimeout(() => {
                this.$refs.ckeditor.instance.focusManager.blur()
                this.showTimeoutDialog = true
            }, 480000) // 480000 = 8min
        },
        clearInactiveTimeout() {
            console.log('Inactive Timeout cleared')
            if (this.inactiveTimeout) {
                clearTimeout(this.inactiveTimeout)
                this.inactiveTimeout = null
            }
        },
        async sendXapi(data) {
            await axios.get('/rest/xapi/projects/' + this.projectId + '/sections/' + this.section.id + data)
        },
        async sendViewXapi() {
            await axios.get('/rest/xapi/projects/' + this.projectId + '/sections/' + this.section.id + '/viewed').then(() => {
                this.viewHandlerEntered = false
            })
        },
        async sendResetTimeout() {
            console.log('Sending refresh to server')
            await axios.patch('/rest/projects/' + this.projectId +
                '/sections/' + this.section.id +
                '/timeout/reset')
        },
        throttleDialogRefresh: _.throttle(function () {
            if (this.inEdit) {
                this.refreshTimeout()
            }
        }, 2000),
        throttleServerRefresh: _.throttle(function () {
            if (this.inEdit) {
                this.sendResetTimeout()
            }
        }, 60000),
        openSection() {
            this.section.isCollapse = 0
            this.$store.dispatch('sections/openSection', {projectId: this.projectId, sectionId: this.section.id})
        },
        closeSection() {
            this.section.isCollapse = 1
            this.$store.dispatch('sections/closeSection', {projectId: this.projectId, sectionId: this.section.id})
        }
    },
    watch: {
        'gtCkeditorReady': function () {
            if (this.gtCkeditorReady) {
                if (this.checkIfSectionIsInViewport()) {
                    this.viewHandlerEntered = true
                    this.viewedSection = true
                    this.checkViewedSection()
                }
            }
        }
    },
    created() {

        window.eventBus.$on('upload-started', function () {
            this.clearInactiveTimeout()
        }.bind(this))
        window.eventBus.$on('upload-running', function () {
            this.throttleServerRefresh()
        }.bind(this))
        window.eventBus.$on('upload-completed', function () {
            this.startInactiveTimeout()
        }.bind(this))
    },
    beforeDestroy() {
        window.eventBus.$off('upload-started')
        window.eventBus.$off('upload-running')
        window.eventBus.$off('upload-completed')
    }
}
</script>

<style lang="scss" scoped>
.section-wrapper {
    background: #fff;

    .bg-gray {
        background: #e4e4e4;
    }
}

.section-toolbar {
    position: absolute;
    left: -4.2rem;
    top: 2rem;
}

.section-expansion-panels{
    z-index: unset;
}
</style>

<style lang="scss">
.section-wrapper {
    margin-top: 0.5rem;
    padding-top: 0.5rem;

    p {
        width: fit-content;
    }

    table {
        max-width: 998px;
    }

    img {
        max-width: 100%;
        height: auto;
    }
}

.ckeditor {
    margin-top: 0.5rem;
    padding-top: 0.5rem;

    p {
        width: fit-content;
        min-width: 5px;
    }

    table {
        max-width: 998px;
    }
}

.ckeditor img {
    max-width: 100%;
    height: auto;
}

.broken-image {
    min-width: 75px;
    min-height: 120px;
    background-image: url('/assets/images/broken-image.svg');
    background-size: cover; /* <------ */
    background-repeat: no-repeat;
    background-position: center center;
}

.ckeditor :focus {
    outline: none
}

.cke {
    width: 998px !important;
    z-index: 179 !important;
}

.cke_top {
    padding: 0 !important;
    display: none !important;
}

.cke_textarea_inline {
    padding-top: 0.5rem;
}

.section-title .v-text-field__slot input {
    color: var(--v-primary-base) !important;
    text-overflow: ellipsis;
}

.section-chat {
    position: absolute;
    right: -4.2rem;
    top: 2rem;
}

@media (max-width: 767px) {

    .ckeditor {
        margin-top: 3rem !important;
    }
    .cke {
        width: 95vw !important;
    }


    .section-toolbar {
        box-shadow: none !important;
        position: relative !important;
        width: 100%;
        left: 0 !important;
        top: 0 !important;

        .v-btn {
            margin: 2px;
        }

        .close-button {
            border-bottom: none !important;
        }

        div {
            flex-direction: row !important;
        }
    }
}

</style>
<style>
    .v-expansion-panel-content__wrap {
        padding: unset;
        flex: unset;
        max-width: unset;
    }
    .v-expansion-panel::before {
        box-shadow: none;
    }
</style>
