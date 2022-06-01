<template>
    <div>
        <v-card
            class="project-card"
            width="auto"
            v-on:click="goToProject"
            :ripple="false"
        >
            <div v-if="project.basicCourse" class="basic-course-indicator">
            </div>
            <div class="project-content">
                <div class="project-card-header">
                    <v-card-title class="project-card-header-title">
                        <div class="text-overline" v-if="project.basicCourse" style="color:var(--v-primary-base)">
                            Basiskurs
                        </div>
                        <span class="">{{ project.title }}</span>
                    </v-card-title>
                    <div class="project-card-header-action" style="">
                        <div>
                            <v-tooltip bottom open-delay="500">
                                <template v-slot:activator="{ on }">
                                    <div v-on="on">
                                        <v-btn tile depressed class="toolbar-button" @click="onToggleWatchProject($event, project)">
                                            <v-icon class="" :class="[project.isUserWatchingProject ? 'button-filled' : 'toolbar-icon']">mdi-eye</v-icon>
                                        </v-btn>
                                    </div>
                                </template>
                                <span>{{ tooltipUserWatchingProject }}</span>
                            </v-tooltip>
                        </div>
                        <div class="additional-toolbar" :class="showAdditionalMenu ? 'toolbar-box-shadow' : ''" v-if="hasAdditionalItems">
                            <v-btn tile depressed class="toolbar-button" @click="toggleAdditionalMenu($event)"
                                   :class="showAdditionalMenu ? 'close-button' : ''">
                                <v-icon class="toolbar-icon">mdi-dots-horizontal</v-icon>
                            </v-btn>
                            <div class="toolbar-box-shadow" v-if="showAdditionalMenu">
                                <div v-if="canChangeProjectType">
                                    <v-tooltip bottom open-delay="500">
                                        <template v-slot:activator="{ on }">
                                            <div v-on="on">
                                                <v-btn tile depressed class="toolbar-button"
                                                       @click="$event.stopPropagation(); $emit('openProjectEditModal', project)">
                                                    <v-icon class="toolbar-icon">mdi-pencil</v-icon>
                                                </v-btn>
                                            </div>
                                        </template>
                                        <span>Forschungsprojekt bearbeiten</span>
                                    </v-tooltip>
                                </div>
                                <div class="toolbar-box-shadow" v-if="canDuplicateProject">
                                    <v-tooltip bottom open-delay="500">
                                        <template v-slot:activator="{ on }">
                                            <div v-on="on">
                                                <v-btn tile depressed class="toolbar-button"
                                                       @click="onDuplicateProject($event, project.id)">
                                                    <v-icon class="toolbar-icon">mdi-content-duplicate</v-icon>
                                                </v-btn>
                                            </div>
                                        </template>
                                        <span>Forschungsprojekt duplizieren</span>
                                    </v-tooltip>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <v-card-text class="project-card-text">
                    <div class="description-text">
                        {{ project.description }}
                    </div>
                </v-card-text>
            </div>
        </v-card>
    </div>
</template>

<script>
import {createNamespacedHelpers} from 'vuex'

const {mapGetters} = createNamespacedHelpers('projects')
export default {
    name: "ProjectsCard",
    props: {
        project: {
            default: null,
            type: Object
        },
        meta: {
            type: Object
        }
    },
    data() {
        return {
            showAdditionalMenu: false
        }
    },
    computed: {
        ...mapGetters(['getProjectTypes']),
        linkName() {
            switch (this.project.type) {
            case this.getProjectTypes.TEMPLATE.value: case this.getProjectTypes.PROJECT_TEMPLATE.value:
                return "Zur Vorlage"
            case this.getProjectTypes.ARCHIVED.value:
                return "Zum archivierten Projekt"
            }
            return "Zum Projekt"
        },
        canDuplicateProject() {
            return this.meta.canDuplicateProject && this.project.type !== this.getProjectTypes.TEMPLATE.value
        },
        canChangeProjectType() {
            return this.meta.canChangeProjectType && this.project.type !== this.getProjectTypes.TEMPLATE.value
        },
        tooltipUserWatchingProject() {
            return this.project.isUserWatchingProject ?
                'Projekt nicht mehr beobachten' :
                'Mit einem Klick auf das Auge-Icon bleibst du über Änderungen in dem Projekt immer auf dem Laufenden'
        },
        hasAdditionalItems() {
            return this.canDuplicateProject || this.canChangeProjectType
        }
    },
    methods: {
        toggleAdditionalMenu(event) {
            event.stopPropagation()
            this.showAdditionalMenu = !this.showAdditionalMenu
        },
        showEditModal() {
            this.showModal = true
        },
        onDuplicateProject(event, projectId) {
            event.stopPropagation()
            this.$store.dispatch('projects/duplicateProject', projectId).then(() => {
                this.$store.dispatch('notifications/newNotification', {
                    message: 'Kopie wurde erfolgreich erstellt',
                    type: 'success'
                })
            }).catch((error) => {
                if(error.status === 404){
                    this.$store.dispatch('notifications/newNotification', {
                        message: 'Projekt konnte nicht dupliziert werden',
                        type: 'error'
                    })
                    return
                }
                if(error.status === 403){
                    this.$store.dispatch('notifications/newNotification', {
                        message: 'Sie sind nicht berechtigt das Projekt zu duplizieren',
                        type: 'error'
                    })
                    return
                }
                this.$store.dispatch('notifications/newNotification', {
                    message: error.data.message,
                    type: 'error'
                })
            })
        },
        onToggleWatchProject(event, project) {
            event.stopPropagation()
            this.$store.dispatch('projects/toggleWatchProject', project).then(() => {
                if(project.isUserWatchingProject){
                    this.$store.dispatch('notifications/newNotification', {
                        message: 'Projekt wurde auf beobachten gesetzt',
                        type: 'success'
                    })
                }else{
                    this.$store.dispatch('notifications/newNotification', {
                        message: 'Projekt wurde auf nicht beobachten gesetzt',
                        type: 'success'
                    })
                }
            }).catch((error) => {
                if(project.isUserWatchingProject){
                    this.$store.dispatch('notifications/newNotification', {
                        message: 'Projekt konnte nicht auf beobachten gesetzt werden',
                        type: 'error'
                    })
                }else{
                    this.$store.dispatch('notifications/newNotification', {
                        message: 'Projekt konnte nicht auf nicht beobachten gesetzt werden',
                        type: 'error'
                    })
                }
            })
        },
        goToProject() {
            this.$router.push({ name: 'project', params: { projectId: this.project.id , projectType: this.project.type} })
        }
    }
}
</script>

<style lang="scss" scoped>
    .button-filled {
        color: var(--v-primary-base) !important;
    }

    .basic-course-indicator {
        width: 0.6rem;
        background-color: var(--v-primary-base);
        align-self: stretch;
        flex-shrink: 0;
        border-bottom-left-radius: inherit !important;
        border-top-left-radius: inherit !important;
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }

    .project-card {
        min-width: 300px;
        margin: 1em;
        display: flex;
        height: 9rem;
    }

    .project-card-text {
        height: 3.8rem;
        overflow-y: auto;
        padding: 0 0.5rem 0.5rem;
        overflow-x: hidden;
        -ms-overflow-style: none;  /* Internet Explorer 10+ */
        scrollbar-width: none;  /* Firefox */
    }

    .project-card-text::-webkit-scrollbar {
        display: none;  /* Safari and Chrome */
    }

    .project-card-header {
        display: flex;
        align-items: baseline;
    }

    .project-card-header-title {
        flex-grow: 1;
        padding: 0.5rem;
        flex-direction: column;
        align-items: baseline;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        display: block;
    }

    .project-card-header-action {
        text-align: center;
        display: flex;
        align-self: flex-start;
        max-height: 40px;
        padding: 0.5rem;
    }

    .toolbar {
        box-shadow: 0 0 5px 0 rgba(0, 0, 0, 0.22);
    }

    .additional-toolbar {
        margin-left: 3px;
    }

    .toolbar-button.v-btn {
        min-width: 45px;
        padding: 5px;
        background-color: #ffffff!important;
    }

    .toolbar-box-shadow {
        box-shadow: 0 3px 1px -2px rgb(0 0 0 / 20%), 0 2px 2px 0 rgb(0 0 0 / 14%), 0 1px 5px 0 rgb(0 0 0 / 12%);
    }

    .close-button {
        border-bottom: 1px solid rgba(0, 0, 0, .54);
    }

    .toolbar-icon {
        color: rgba(0, 0, 0, .54) !important;
    }

    .ellipsis {
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }

    .description-text {
      padding-top: 0.5rem;
      max-height: 3rem;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
      text-overflow: ellipsis;
      display: -webkit-box;
      margin-bottom: 0.5rem;
      word-break: break-word;
    }

    .project-content {
      flex-grow: 1;
      min-width: 0;
    }
</style>
