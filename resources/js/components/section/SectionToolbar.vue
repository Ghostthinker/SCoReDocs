<template>
    <div>
        <template v-if="!section.isCollapse">
            <div class="toolbar mb-2">
                <v-btn tile depressed class="toolbar-button" :disabled="inEdit" @click="$emit('close-section')">
                    <v-icon class="toolbar-icon">mdi-chevron-up</v-icon>
                </v-btn>
            </div>
            <v-tooltip bottom v-if="section.username">
                <template v-slot:activator="{ on, attrs }">
                    <v-icon class="locked-indicator"
                            v-bind="attrs"
                            v-on="on"
                            v-show="isLock && !inEdit">mdi-lock
                    </v-icon>
                </template>
                <span>Dieser Abschnitt wurde am {{ section.lockedAt }} von dem Anwender {{ section.username }}
                    gesperrt</span>
            </v-tooltip>
            <div class="toolbar">
                <v-btn v-if="!inEdit && !isLock" :disabled="!userIsEntitledToChangeSection" tile depressed
                       class="toolbar-button" @click="editSection" value="edit">
                    <v-icon class="toolbar-icon">mdi-pencil</v-icon>
                </v-btn>
                <div v-if="inEdit" class="button-Container">
                    <v-btn tile depressed class="toolbar-button close-button" @click="openResetDialog">
                        <v-icon class="toolbar-icon">mdi-close</v-icon>
                    </v-btn>
                    <custom-dialog :dialog="dialog" show-abort-button @close="dialog = false">
                        <template v-slot:title>
                            Änderungen verwerfen
                        </template>
                        <template v-slot:content>
                            Nicht gespeicherte Änderungen am Abschnitt werden verworfen. Möchten Sie fortfahren?
                        </template>
                        <template v-slot:actions>
                            <v-btn
                                color="primary"
                                @click="resetSection"
                            >
                                Verwerfen
                            </v-btn>
                        </template>
                    </custom-dialog>
                    <v-tooltip bottom :disabled="hasChanges" open-delay="500">
                        <template v-slot:activator="{ on }">
                            <div v-on="on">
                                <v-btn tile depressed
                                       class="toolbar-button"
                                       @click="saveSection"
                                       value="save"
                                       :disabled="!hasChanges">
                                    <v-icon class="toolbar-icon">mdi-content-save</v-icon>
                                </v-btn>
                            </div>
                        </template>
                        <span>Keine Änderungen</span>
                    </v-tooltip>
                </div>
            </div>
            <div class="toolbar mt-2">
                <v-btn tile depressed class="toolbar-button" @click="$emit('start-playlist', section.id)">
                    <v-icon class="toolbar-icon">mdi-play</v-icon>
                </v-btn>
            </div>
            <div class="toolbar mt-2">
                <v-btn tile depressed class="toolbar-button" @click="toggleAdditionalMenu"
                       :class="showAdditionalMenu ? 'close-button' : ''">
                    <v-icon class="toolbar-icon">mdi-dots-horizontal</v-icon>
                </v-btn>
                <div v-if="showAdditionalMenu" class="button-Container">
                    <v-tooltip bottom open-delay="500">
                        <template v-slot:activator="{ on }">
                            <div v-on="on">
                                <v-btn tile depressed
                                       class="toolbar-button"
                                       disabled>
                                    <v-icon class="toolbar-icon">mdi-link-variant</v-icon>
                                    <v-badge :content="getCount" color="primary">
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
                                       class="toolbar-button"
                                       @click="generateLink">
                                    <v-icon class="toolbar-icon">mdi-link</v-icon>
                                </v-btn>
                            </div>
                        </template>
                        <span>Link zum Abschnitt in die Zwischenablage kopieren</span>
                    </v-tooltip>
                    <v-tooltip bottom open-delay="500">
                        <template v-slot:activator="{ on }">
                            <div v-on="on">
                                <v-btn tile depressed
                                       class="toolbar-button"
                                       @click="generateLinkByDialog">
                                    <v-icon class="toolbar-icon">mdi-link-plus</v-icon>
                                </v-btn>
                            </div>
                        </template>
                        <span>Link zum Abschnitt mit Anzeigetext erstellen</span>
                    </v-tooltip>
                    <v-tooltip bottom open-delay="500">
                        <template v-slot:activator="{ on }">
                            <div v-on="on">
                                <v-btn tile depressed
                                       class="toolbar-button"
                                       @click="downloadPlaylist"
                                >
                                    <v-icon class="toolbar-icon">mdi-download</v-icon>
                                </v-btn>
                            </div>
                        </template>
                        <span>Playlist herunterladen</span>
                    </v-tooltip>
                    <v-tooltip bottom open-delay="500">
                        <template v-slot:activator="{ on }">
                            <div v-on="on">
                                <v-btn tile depressed
                                       class="toolbar-button"
                                       @click="$emit('show-audits')"
                                >
                                    <v-icon class="toolbar-icon">mdi-clock</v-icon>
                                </v-btn>
                            </div>
                        </template>
                        <span>Versionen vergleichen</span>
                    </v-tooltip>
                    <div>
                        <v-btn v-if="!inEdit && canDelete" tile depressed class="toolbar-button" @click="deleteSection">
                            <v-icon class="toolbar-icon">mdi-delete</v-icon>
                        </v-btn>
                        <v-btn v-else disabled tile depressed class="toolbar-button" value="cancel">
                            <v-icon class="toolbar-icon">mdi-delete-off</v-icon>
                        </v-btn>
                    </div>
                </div>
            </div>
        </template>
        <template v-else>
            <div class="toolbar">
                <v-btn tile depressed class="toolbar-button" @click="$emit('open-section')">
                    <v-icon class="toolbar-icon">mdi-chevron-down</v-icon>
                </v-btn>
            </div>
        </template>
    </div>
</template>

<script>
import CustomDialog from "../dialog/CustomDialog"
import axios from 'axios'

export default {
    name: "SectionToolbar",
    components: {CustomDialog},
    props: {
        section: {
            type: Object,
            required: true
        },
        content: {
            default: "0",
        },
        inEdit: {
            default: true,
            type: Boolean
        },
        isLock: {
            default: false,
            type: Boolean
        },
        hasChanges: {
            default: false,
            type: Boolean
        },
        canDelete: {
            default: false,
            type: Boolean
        }
    },
    data() {
        return {
            showAdditionalMenu: false,
            count: '0',
            dialog: false
        }
    },
    computed: {
        getCount() {
            return this.count || '0'
        },
        userIsEntitledToChangeSection() {
            return this.section.userIsEntitledToChangeContent || this.section.userIsEntitledToChangeHeadingType || this.section.userIsEntitledToChangeStatus
        }
    },
    methods: {
        saveSection() {
            this.$emit('saved')
        },
        deleteSection() {
            this.$emit('delete')
        },
        resetSection() {
            this.dialog = false
            this.$emit('reseted')
        },
        editSection() {
            this.$emit('edit')
        },
        generateLink() {
            this.$emit('generate-link')
        },
        generateLinkByDialog() {
            this.$emit('generate-link-by-dialog')
        },
        openResetDialog() {
            this.dialog = true
            this.$emit('open-reset-dialog')
        },
        toggleAdditionalMenu() {
            this.showAdditionalMenu = !this.showAdditionalMenu
            if (this.showAdditionalMenu) {
                this.count = '0'
                this.$store.dispatch('sections/fetchElementLinkCount', 'Section-' + this.section.id).then((value) => {
                    this.count = value
                })
            }
        },
        closeHistoryDialog() {
            this.showAudits = false
        },
        openDownloadPlaylistUrl(playlistId) {
            axios.get('/rest/section/' + this.section.id + '/playlist/download/' + playlistId).then((response) => {
                window.open(response.data)
            })
        },
        downloadPlaylist() {
            axios.get('/rest/section/' + this.section.id + '/playlist/download').then((response) => {
                switch (response.data.status) {
                case 200:
                    this.openDownloadPlaylistUrl(response.data.data.key)
                    break
                case 202:
                    this.$store.dispatch('notifications/persistentNotification', {
                        message: 'Playlist wird aufbereitet',
                        type: 'success',
                        isPlaylistDownload: true,
                        sectionId: this.section.id,
                        playlistId: response.data.data.key
                    })
                    break
                case 422:
                    this.$store.dispatch('notifications/persistentNotification', {
                        message: 'Im gewählten Abschnitt sind nicht genügend Videos/Sequenzen für eine herunterladbare Playlist vorhanden. ' +
                            'Es müssen mindestens 2 Videos und oder Sequenzen vorhanden sein.',
                        type: 'error'
                    })
                    break
                default:
                    this.$store.dispatch('notifications/persistentNotification', {
                        message: 'Beim herunterladen der Playlist oder deren Vorbereitung ist ein Fehler aufgetreten.' +
                            'Bitte versuchen sie es erneut. Sollte der Fehler weierhin auftreten kontaktieren' +
                            'Sie den Support.',
                        type: 'error'
                    })
                }
            }).catch(err => {
                this.$store.dispatch('notifications/newNotification', {
                    message: 'Playlist konnte nicht heruntergeladen werden.',
                    type: 'error'
                })
            })
        },
    },
    mounted() {
        this.projectId = this.$route.params.projectId
    }
}
</script>

<style lang="scss" scoped src="./toolbar.scss"></style>
