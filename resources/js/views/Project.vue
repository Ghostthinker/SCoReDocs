<template>
    <default>
        <template v-slot:left-menu-title>
            <project-outline-header></project-outline-header>
        </template>
        <template v-slot:left-menu>
            <project-outline :sections="getSortedSections" :projectId="projectId"></project-outline>

            <div v-if="!isAllSectionsLoaded" class="skeleton outline">
                <v-skeleton-loader
                    type="article"
                ></v-skeleton-loader>
            </div>
        </template>
        <template v-slot:content-header>
            <div class="project-header pa-2">
                <div class="menu-icon" @click="navigateToDashboard">
                    <v-btn icon>
                        <v-icon large>mdi-chevron-left</v-icon>
                    </v-btn>
                </div>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on, attrs }">
                        <div
                            v-bind="attrs"
                            v-on="on"
                            class="project-title py-4"
                        >
                            {{ getProject.title }}
                        </div>
                    </template>
                    <span>{{ getProject.title }} </span>
                </v-tooltip>
            </div>
        </template>
        <template v-slot:content-body>
            <div id="sections" ref="sections" class="sections-wrapper">
                <add-section-line
                    v-if="getSections.length === 0"
                    show-permanent="true"
                ></add-section-line>
                <div
                    v-for="section in getSortedSections"
                    :key="section.id"
                >
                    <section-view :section="section" :projectId="projectId"
                                  @ckEditorReady="sectionEditorReady(section.id)"
                                  @start-playlist="startPlayList"
                                  class="section"
                    ></section-view>
                </div>
                <div v-if="!isAllSectionsLoaded" class="skeleton">
                    <v-skeleton-loader
                        type="article"
                    ></v-skeleton-loader>
                </div>
                <media-form v-show="showMediaForm" :inputData.sync="showMediaForm"
                            v-on:upload-completed="handleUploadCompleted"></media-form>
                <custom-dialog :dialog="videoAlert.show" @close="dismissVideoAlert">
                    <template v-slot:title>
                        {{ videoAlert.title }}
                    </template>
                    <template v-slot:content>
                        {{ videoAlert.message }}
                    </template>
                </custom-dialog>
            </div>
            <custom-dialog :dialog="showDialogUnavailable" @close="showDialogUnavailable = false">
                <template v-slot:title>
                    Fehlender Abschnitt
                </template>
                <template v-slot:content>
                    Der Abschnitt ist nicht mehr vorhanden!
                </template>
            </custom-dialog>
            <Link-elements-toolbar v-if="showCkEditorImageMenuBox"
                                   :ckEditorImageMenuBox="ckEditorImageMenuBox"></Link-elements-toolbar>
        </template>
        <template v-slot:content-footer>
            <div class="social-container">
                <media-player-box
                    :showPlayer="showPlayer"
                    :annotationId="annotationId"
                    :activeMediaId="activeMediaId"
                    :sequenceId="sequenceId"
                    :playListData="playListData"
                    @closePlayer="closePlayer"
                ></media-player-box>
                <Chat :projectId="projectId"></Chat>
            </div>
        </template>
        <template v-slot:mentioning>
            <MentioningContainer :project-id="projectId"></MentioningContainer>
        </template>
        <template v-slot:right-menu>
            <activity-feed :is-in-project="true" :projectId="projectId"></activity-feed>
        </template>
    </default>
</template>

<script>
import AddSectionLine from "../components/section/AddSectionLine"
import SectionView from "../components/section/Section"
import Chat from "../components/chat/Chat"
import MediaForm from "../components/media/MediaForm"
import MediaPlayerBox from "../components/media/MediaPlayerBox"
import ProjectOutline from "../components/projects/ProjectOutline"
import LinkElementsToolbar from "../components/section/LinkElementsToolbar"
import Section from "../models/section"
import axios from "axios"
import Media, {MediaTypes} from "../models/media"
import MentioningContainer from "../components/notifications/mentioning/MentioningContainer"
import Default from "../layouts/Default"
import ActivityFeed from "../components/activities/ActivityFeed"
import {createNamespacedHelpers} from 'vuex'
import CustomDialog from "../components/dialog/CustomDialog"
import ProjectOutlineHeader from "../components/projects/ProjectOutlineHeader"

const {mapGetters, mapActions} = createNamespacedHelpers('sections')

export default {
    name: "Project",
    components: {
        CustomDialog,
        ProjectOutlineHeader,
        Default,
        Chat,
        SectionView,
        AddSectionLine,
        MediaForm,
        MediaPlayerBox,
        ProjectOutline,
        LinkElementsToolbar,
        MentioningContainer,
        ActivityFeed
    },
    data() {
        return {
            projectId: null,
            projectType: null,
            sequenceId: null,
            showMediaForm: false,
            ckEditorId: null,
            showPlayer: false,
            showCkEditorImageMenuBox: false,
            ckEditorImageMenuBox: {},
            activeMediaId: '',
            annotationId: '',
            showDialogUnavailable: false,
            videoAlert: {
                show: false,
                title: '',
                message: ''
            },
            ckEditorsLoaded: 0,
            playListData: '',
            initSectionId: null,
            user: null
        }
    },
    methods: {
        ...mapActions(['fetchSections', 'insertSection', 'updateSection', 'getSection', 'removeSection', 'fetchSectionsByUrl', 'resetSections']),

        async onSectionCreated(sectionId, indexArray) {
            let newSection
            try {
                const response = await this.getSection({'projectId': this.projectId, 'sectionId': sectionId})
                newSection = Section.create(response.data)
                this.insertSection(newSection)
            } catch (e) {
                console.error(e)
            }

            try {
                indexArray.forEach(el => {
                    let section = this.getSections.find(sec => sec.id === el.id)
                    section.index = el.index
                    this.updateSection(section)
                })
                this.$router.replace({hash: '#Section-' + sectionId})
                if (this.getSortedSections[this.getSortedSections.length - 1].index === newSection.index) {
                    this.scrollToHash()
                }
            } catch (e) {
                console.error(e)
            }

        },
        async onSectionDeleted(sectionId) {
            let section = this.getSections.find(sec => sec.id === sectionId)
            console.log(section)

            try {
                this.removeSection(sectionId)
            } catch (e) {
                console.error(e)
            }
        },
        async onSectionReload(sectionId, section = null) {
            const sectionIndex = _.findIndex(this.getSections, {'id': parseInt(sectionId)})
            if (sectionIndex > -1) {
                if (!section) {
                    const response = await this.getSection({'projectId': this.projectId, 'sectionId': sectionId})
                    section = Section.create(response.data)
                }
                // Resetting oldContent
                section.oldContent = section.content
                section.oldTitle = section.title
                section.oldHeading = section.heading
                section.oldStatus = section.status
                section.oldStatusText = section.statusText
                this.updateSection(section)
            }
        },
        initEcho() {
            Echo.channel('section.' + this.projectId)
                .listen('.newSection', (e) => {
                    if (this.isValidProjectId(e.projectId)) {
                        this.onSectionCreated(e.sectionId, JSON.parse(e.indexArray))
                    }
                })
                .listen('.deleteSection', (e) => {
                    if (e.projectId != this.projectId) {
                        return
                    }
                    this.onSectionDeleted(e.sectionId)
                })
                .listen('.updateSection', (e) => {
                    if (this.isValidProjectId(e.section.project_id)) {
                        this.onSectionReload(e.section.id, e.section)
                    }
                })
                .listen('.lockSection', (e) => {
                    if (this.isValidProjectId(e.projectId)) {
                        this.onSectionReload(e.sectionId)
                        window.eventBus.$emit('reloadSectionLock')
                    }
                })
                .listen('.reloadSection', (e) => {
                    if (this.isValidProjectId(e.projectId)) {
                        this.onSectionReload(e.sectionId)
                    }
                })
            this.$store.dispatch('user/fetchUser').then((user) => {
                this.user = user
                Echo.private('user.' + user.id)
                    .listen('.userWatchesProject', () => {
                        this.$store.dispatch('notifications/newNotification', {
                            message: 'Super, dass Du hier aktiv geworden bist! Du beobachtest nun dieses Forschungsprojekt, um nichts mehr zu verpassen!',
                            type: 'success',
                            duration: 10000
                        })
                    })
                Echo.private('messageMention.' + this.projectId + '-user.' + this.user.id)
                    .listen('.newMessageMention', (messageMentioning) => {
                        this.$store.dispatch('messages/pushMessageMentioning', messageMentioning)
                    })
            })
        },
        isValidProjectId(projectId) {
            if (this.projectId != projectId) {
                console.error('Broadcast Error. Project ID mismatch. Project ID: ' + projectId)
                return false
            }
            return true
        },
        handleUploadCompleted(previewData) {
            window.eventBus.$data.videoUploadDone = false
            window.eventBus.$emit('videoUploadDone', previewData.data)
            this.showMediaForm = false
        },
        scrollMeTo(element) {
            element.scrollIntoView()
        },
        scrollToHash() {
            let ref = this.$route.hash.substring(1)
            let element = null
            let self = this

            let match = ref.match(/Section-(\d+)|Sec-(\d+)/gm)
            if(match && match.length > 0){
                const sectionId = match[0].split('-')[1]
                let section = this.$store.getters['sections/getSectionById'](sectionId)
                if(section){
                    section.isCollapse = 0
                    this.$store.dispatch('sections/openSection', {projectId: this.projectId, sectionId: section.id})
                }
            }

            setTimeout(() => {
                element = document.getElementById(ref)
                if (element == null) {
                    element = document.querySelector('[ref="' + ref + '"]')
                }

                if (element != null) {
                    self.scrollMeTo(element)
                }
            }, 250)

        },
        beforeUnloadEvent() {
            axios.get('/rest/xapi/projects/' + this.projectId + '/leftproject')
        },
        visibilitychangeEvent() {
            if (document.visibilityState !== "visible") {
                axios.get('/rest/xapi/projects/' + this.projectId + '/leftproject')
            } else {
                axios.get('/rest/xapi/projects/' + this.projectId + '/openproject')
            }
        },
        showLinksNotWorkingAlert() {
            if (this.$route.params.projectType === "Template") {
                this.$store.dispatch('notifications/newNotification', {
                    message: 'Achtung, Verlinkungen von Abschnitten und Inhalten innerhalb dieser Vorlage werden nicht in die Kopien der Nutzer übernommen. Verlinkungen auf externe Quellen hingegen werden übernommen.',
                    type: 'warning',
                    duration: 10000
                })
            }
        },
        dismissVideoAlert() {
            this.videoAlert.show = false
            this.videoAlert.title = ''
            this.videoAlert.message = ''
        },
        sectionEditorReady() {
            this.ckEditorsLoaded++
            if (this.ckEditorsLoaded === this.getSections.length) {
                // Wait for images to be loaded
                Promise.all(Array.from(document.images).filter(img => !img.complete).map(img => new Promise(resolve => {
                    img.onload = img.onerror = resolve
                }))).then(() => {
                    window.eventBus.$emit('sectionsContentLoaded')
                })
                Promise.all(Array.from(document.images).filter(img => !img.complete).map(img => new Promise(resolve => {
                    img.onerror = resolve
                }).then(err => {
                    err.target.classList.add("broken-image")
                })))
            }
        },
        startPlayList(sectionId) {
            this.closePlayer()

            this.activeMediaId = null
            this.sequenceId = null

            axios.get('/rest/section/' + sectionId + '/playlist').then((response) => {
                const data = response.data
                if (data.isPending) {
                    this.$store.dispatch('notifications/newNotification', {
                        message: 'Playlist beinhaltet nicht alle Videos, es befinden sich noch welche in Konvertierung.',
                        type: 'warning'
                    })
                }
                if (!data.isPending && data.media.length === 0) {
                    this.$store.dispatch('notifications/newNotification', {
                        message: 'Keine Videos in diesem Abschnitt für eine Playlist gefunden.',
                        type: 'warning'
                    })
                    return
                }
                if (data.media[0].type === "Video") {
                    this.activeMediaId = data.media[0].id
                    this.sequenceId = null
                } else {
                    this.activeMediaId = data.media[0].video_nid
                    this.sequenceId = data.media[0].id
                }
                this.playListData = data
                setTimeout(() => {
                    this.showPlayer = true
                }, 150)
            }).catch(() => {
                this.$store.dispatch('notifications/newNotification', {
                    message: 'Playlist konnte nicht gestartet werden.',
                    type: 'error'
                })
            })
        },
        closePlayer() {
            this.showPlayer = false
            this.playListData = ''
        },
        listenMouse(e) {
            if (e.target.nodeName === 'IMG') {

                this.elementPosRelative = e.target.getBoundingClientRect()
                const elementPos = {
                    left: this.elementPosRelative.left + window.scrollX,
                    top: this.elementPosRelative.top + window.scrollY,
                    id: e.target.id || null,
                    ref: e.target.getAttribute('ref') || null
                }

                window.eventBus.$emit('mouseoverImg', elementPos)

                this.mouseOverelement = document.getElementById('content')
                this.mouseOverelement.addEventListener('mousemove', this.listenMousemove)
            }
        },
        listenMousemove(e) {

            if(e.target.nodeName === 'IMG' || !this.showCkEditorImageMenuBox) {
                return
            }

            if(e.target.nodeName === 'IMG') {
                this.elementPosRelative = e.target.getBoundingClientRect()
            } else {
                this.elementPosRelative = document.getElementById('linkElementToolbar').getBoundingClientRect()
            }

            let outWidth = e.clientX < this.elementPosRelative.left || e.clientX > this.elementPosRelative.right
            let outHeight = e.clientY < this.elementPosRelative.top || e.clientY > this.elementPosRelative.bottom

            if (outWidth || outHeight) {
                window.eventBus.$emit('mouseoutImg')
                this.mouseOverelement.removeEventListener('mousemove', this.listenMousemove)
            }
        },
        navigateToDashboard() {
            this.$router.push({name: 'projects'})
        },
        fetchSectionsPaginate(response) {
            return this.fetchSectionsByUrl(response.data.links.next).then((response) => {
                if(response.data.links && response.data.links.next) {
                    this.fetchSectionsPaginate(response)
                }
            })
        },
        fetchSectionsInit(data) {
            this.fetchSections(data).then((response) => {
                let hasNotLinks = !response.data.links || !response.data.links.next
                if (this.getSortedSections.length > 0 && hasNotLinks) {
                    setTimeout(() => {
                        this.scrollToHash()
                        let messageId = this.$route.query.messageId
                        if (messageId) {
                            this.openChat(messageId)
                        }
                    }, 200)
                }
                if (response.data.links && response.data.links.next) {
                    this.fetchSectionsPaginate(response)
                }
            }).catch(err => {
                if (err.response.status === 403) {
                    this.$router.push({name: 'projects'})
                }
            })
        },
        openChat(messageId) {
            let mentioningId = this.$route.query.mentioningId

            if(mentioningId) {
                this.$store.dispatch('messages/markMessageMentioningAsRead', mentioningId)
            }


            //get Section title if sectionId is set
            let section = false
            if (this.initSectionId) {
                section = this.$store.getters['sections/getSectionById'](this.initSectionId)
            }

            let title = section ? section.title : this.getProject.title
            this.$store.dispatch('messages/setActiveChat', {
                sectionId: this.initSectionId,
                projectId: this.projectId,
                messageId: Number(messageId),
                open: true,
                title: title
            })
        }
    },
    computed: {
        ...mapGetters(['getSortedSections', 'getSections', 'isAllSectionsLoaded']),
        getProject() {
            return this.$store.getters["projects/getProject"]
        }
    },
    mounted() {
        this.showLinksNotWorkingAlert()
        // check seems that the sections are not the right scope
        let contentArea = document.getElementById('content')
        contentArea.addEventListener('mouseover', this.listenMouse)

        if (!this.CKEClickEventsRegistered) {
            this.CKEClickEventsRegistered = true
            contentArea.addEventListener('click', function (e) {
                // enables opening videos links etc with a left click
                if (e.target.id) {
                    let urlRoot = location.protocol + '//' + location.host + location.pathname

                    if (e.target.id.startsWith('Annotation')) {
                        window.eventBus.$emit('clickedAnnotationImage', e.target.id)
                    }
                    if (e.target.id.startsWith('Image')) {
                        window.eventBus.$emit('clickedPreviewImage', e.target.id)
                    }
                    if (e.target.id.startsWith('Sequence')) {
                        window.eventBus.$emit('clickedSequenceImage', e.target.id)
                    }

                    if (e.target.id.startsWith('scoreLink')) {
                        if (e.target.href.indexOf(urlRoot) > -1) {
                            window.location.href = e.target.href
                            window.eventBus.$emit('clickedScoreLink', e.target)
                        } else {
                            window.open(e.target.href)
                        }
                    }
                }
            })
        }
    },
    created() {
        this.resetSections()
        this.projectId = this.$route.params.projectId

        this.initEcho()

        let ref = this.$route.hash.substring(1)
        let match = ref.match(/Section-(\d+)|Sec-(\d+)/gm)
        if(match && match.length > 0){
            this.initSectionId = match[0].split('-')[1]
            let data = {projectId: this.projectId, paginate: false}
            this.fetchSectionsInit(data)
        } else {
            let data = {projectId: this.projectId, paginate: true}
            this.fetchSectionsInit(data)
        }

        this.$store.dispatch('projects/fetchProject', this.projectId).then(() => {
        }).catch(err => {
            if (err.response.status === 404) {
                this.$router.push({name: 'projects'})
                this.$store.dispatch('notifications/newNotification', {
                    message: 'Dieses Projekt existiert nicht.',
                    type: 'warning'
                })
            }
        })

        window.eventBus.$on('sectionsContentLoaded', function () {
            this.scrollToHash()
        }.bind(this))

        window.eventBus.$on('triggerVideoUpload', function () {
            this.showMediaForm = true
        }.bind(this))

        window.eventBus.$on('mouseoverImg', function (pos) {
            this.showCkEditorImageMenuBox = true
            this.ckEditorImageMenuBox.left = pos.left
            this.ckEditorImageMenuBox.top = pos.top
            this.ckEditorImageMenuBox.id = pos.id
            this.ckEditorImageMenuBox.ref = pos.ref
        }.bind(this))

        window.eventBus.$on('mouseoutImg', function () {
            this.showCkEditorImageMenuBox = false
        }.bind(this))

        window.eventBus.$on('clickedScoreLink', function (target) {
            if (!target.hash) {
                return
            }
            let secId = target.hash.substring(1)
            if (document.getElementById(secId) !== null) {
                this.scrollToHash()
                return
            } else if (!secId.startsWith('Section')) {
                this.scrollToHash()
                return
            }
            this.showDialogUnavailable = true
        }.bind(this))

        window.eventBus.$on('clickedPreviewImage', function (payload) {
            let preString = 'Image'
            this.activeMediaId = payload.replace(preString, '')
            this.annotationId = ''
            this.sequenceId = null

            this.closePlayer()

            axios.get('/rest/media/' + this.activeMediaId).then((response) => {
                const media = Media.create(response.data)
                switch (media.status) {
                case MediaTypes.CREATED:
                    this.videoAlert.show = true
                    this.videoAlert.title = 'Fehler bei Konvertierung'
                    this.videoAlert.message = 'Es gab ein Fehler bei der Konvertierung, bitte laden Sie das Video erneut hoch. Bitte wenden Sie sich an den Support, falls das Problem bestehen bleibt.'
                    return
                case MediaTypes.PENDING:
                    this.videoAlert.show = true
                    this.videoAlert.title = 'Video wird konvertiert'
                    this.videoAlert.message = 'Video wird für die Anzeige aufbereitet.'
                    return
                case MediaTypes.FAILED_CONVERT:
                    this.videoAlert.show = true
                    this.videoAlert.title = 'Video wurde nicht konvertiert'
                    this.videoAlert.message = 'Video konnte für die Anzeige nicht aufbereitet werden, bitte laden Sie das Video erneut hoch. Bitte wenden Sie sich an den Support, falls das Problem bestehen bleibt.'
                    return
                default:
                    break
                }
                setTimeout(() => {
                    this.showPlayer = true
                }, 150)
            }, error => {
                console.error('Can not reveive media. Continuing as normal. Error: ', error)
                setTimeout(() => {
                    this.showPlayer = true
                }, 150)

            })
        }.bind(this))

        window.eventBus.$on('clickedAnnotationImage', function (payload) {
            let preStringAnnotation = 'Annotation'
            let preStringMedia = 'Media'
            let preStringSequence = 'Sequence'
            this.sequenceId = null

            let tempStringComplete = payload.split(preStringSequence)
            if ((tempStringComplete[1])) {
                this.sequenceId = tempStringComplete[1]
            }

            let tempString = tempStringComplete[0].split(preStringMedia)
            this.annotationId = tempString[0].replace(preStringAnnotation, '')
            this.activeMediaId = tempString[1]

            this.closePlayer()

            setTimeout(() => {
                this.showPlayer = true
            }, 150)

        }.bind(this))

        window.eventBus.$on('clickedSequenceImage', function (payload) {
            let preStringSequence = 'Sequence'
            let preStringMedia = 'Media'

            let tempString = payload.split(preStringMedia)
            this.sequenceId = tempString[0].replace(preStringSequence, '')


            this.activeMediaId = tempString[1]
            this.closePlayer()

            setTimeout(() => {
                this.showPlayer = true
            }, 150)

        }.bind(this))

        window.addEventListener('beforeunload', this.beforeUnloadEvent)
        document.addEventListener('visibilitychange', this.visibilitychangeEvent)
    },
    beforeDestroy() {
        window.eventBus.$off("clickedSequenceImage")
        window.eventBus.$off("clickedAnnotationImage")
        window.eventBus.$off("clickedPreviewImage")
        window.eventBus.$off("clickedScoreLink")
        window.eventBus.$off("mouseoutImg")
        window.eventBus.$off("mouseoverImg")
        window.eventBus.$off("triggerVideoUpload")
        window.eventBus.$off("sectionsContentLoaded")
        window.eventBus.$data.eventListenerInsertedAnnotation = false
        window.eventBus.$data.eventListenerInsertedSequence = false
        window.eventBus.$data.eventListenerVideoUploadDone = false
        window.eventBus.$data.eventListenerClick = false
        Echo.leave('messageMention.' + this.projectId + '-user.' + this.user.id)
    },
    destroyed() {
        window.removeEventListener('beforeunload', this.beforeUnloadEvent)
        document.removeEventListener('visibilitychange', this.visibilitychangeEvent)
        axios.get('/rest/xapi/projects/' + this.projectId + '/leftproject')
    }
}
</script>

<style lang="scss" scoped>

.social-container {
    display: flex;
    flex: 1 1 0;
    flex-wrap: wrap;
    align-items: flex-end;
    justify-content: flex-end;
    position: relative;
    z-index: 1000;
}

.skeleton {
    padding: 0.5em;
}

.sections-wrapper {
    border-left: 2px solid #00000014;
    border-right: 2px solid #00000014;
    height: max-content;
    background-color: white;
    width: 1016px !important;
    min-width: 1016px !important;
}

.section {
    min-height: 200px;
}

.project-header {
    display: flex;
    justify-content: flex-start;
    width: 1016px;
    background-color: white;
    height: 100%;
    align-items: center;
    border-left: 2px solid #00000014;
    border-right: 2px solid #00000014;

  .menu-icon {
    padding-right: 1em;
  }

  .menu-icon .v-icon {
    font-size: 48px !important;
  }

  .project-title {
    word-break: break-word;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    padding-bottom: 15px !important;
  }
}

.v-tooltip__content {
  width: 500px;
  span {
    word-break: break-word;
  }
}

@media (max-width: 767px) {
    .sections-wrapper {
        width: 100% !important;
    }
}
</style>


