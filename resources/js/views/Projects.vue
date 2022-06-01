<template>
    <div>
        <default>
            <template v-slot:content-body>
                <div class="content-header">
                    <span>News</span>
                </div>
                <div class="news-container">
                    <news></news>
                </div>
                <div class="content-header">
                    <span>Meine Projekte</span>
                </div>
                <div class="projects-container">
                    <projects-grid
                        v-if="getMyWatchedProjects.length > 0"
                        v-bind:projects="getMyWatchedProjects"
                        :meta="getProjectsMeta"
                        @openProjectEditModal="showEditDialog($event)">
                        ></projects-grid>
                </div>
            </template>
            <template v-slot:content-footer>
                <v-speed-dial
                    v-model="fab"
                    bottom
                    right
                    transition="slide-y-reverse-transition"
                    class="project-fab"
                >
                    <template v-slot:activator>
                        <v-btn
                            v-model="fab"
                            color="primary"
                            dark
                            fab
                        >
                            <v-icon v-if="fab">
                                mdi-close
                            </v-icon>
                            <v-icon v-else>
                                mdi-plus
                            </v-icon>
                        </v-btn>
                    </template>
                    <v-tooltip left>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                fab
                                dark
                                small
                                color="primary"
                                v-on:click="showDialog()"
                                v-bind="attrs"
                                v-on="on"
                            >
                                <v-icon>mdi-note-plus</v-icon>
                            </v-btn>
                        </template>
                        <span>Projekt erstellen</span>
                    </v-tooltip>
                    <v-tooltip left>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                fab
                                dark
                                small
                                color="primary"
                                v-on:click="openNewsAdd()"
                                v-bind="attrs"
                                v-on="on"
                                v-if="getCanCreatePermission"
                            >
                                <v-icon>mdi-newspaper-variant-outline</v-icon>
                            </v-btn>
                        </template>
                        <span>News schreiben</span>
                    </v-tooltip>
                </v-speed-dial>
            </template>
        </default>
        <div v-if=showEditModal>
            <projects-edit-form  :inputData.sync="showEditModal"
                                 :project="projectToBeEdited"
                                 :headline="'Forschungsprojekt bearbeiten'"
                                 :success-message="'Das Bearbeiten des Projektes war erfolgreich'"
                                 :generic-error-message="'Es gab einen Fehler beim Bearbeiten des Projektes'"
                                 :not-allowed-message="'Nur das SCoRe-Team kann Projekte bearbeiten'"
            ></projects-edit-form>
        </div>
        <projects-form :inputData.sync="showModal"
                       :headline="'Projekt erstellen'"
                       :request-u-r-i="'/rest/project'"
                       :success-message="'Anlegen des Projektes war erfolgreich'"
                       :generic-error-message="'Es gab einen Fehler beim Anlegen des Projektes'"
                       :not-allowed-message="'Nur das SCoRe-Team und Lernbegleiter können ein Projekt anlegen'"
                       @addProject="addProject"></projects-form>
        <news-form
            v-if="showNewsAdd"
            :inputData.sync="showNewsAdd"
            :headline="'News erstellen'"
            :request-u-r-i="'/rest/news'"
            :success-message="'Erstellen einer News war erfolgreich'"
            :generic-error-message="'Es gab einen Fehler beim Erstellen einer News'"
            :not-allowed-message="'Nur das SCoRe-Team kann eine News erstellen'"
        ></news-form>
        <div></div>
        <intro-video></intro-video>
    </div>
</template>

<script>
import ProjectsForm from "../components/projects/ProjectsForm"
import ProjectsEditForm from "../components/projects/ProjectsEditForm"
import ProjectsGrid from "../components/projects/ProjectsGrid"
import Default from "../layouts/Default"
import IntroVideo from "../components/onboarding/IntroVideo"
import NewsForm from "../components/news/NewsForm"
import News from "../components/news/News"
import { mapActions, mapGetters } from "vuex"

export default {
    name: "Projects",
    components: {News, NewsForm, IntroVideo, Default, ProjectsForm, ProjectsEditForm, ProjectsGrid},
    data() {
        return {
            showModal: false,
            version1: Object,
            version2: Object,
            showEditModal: false,
            projectToBeEdited: null,
            showNewsAdd: false,
            fab: false,
        }
    },
    computed: {
        ...mapGetters({
            getProjects: 'projects/getProjects',
            getProjectsMeta: 'projects/getProjectsMeta',
            getAllProjects: 'projects/getAllProjects',
            getCanCreatePermission: 'news/getCanCreatePermission'
        }),
        getMyWatchedProjects() {
            return this.getAllProjects.filter(project => project.isUserWatchingProject)
        }
    },
    methods: {
        ...mapActions({fetchProjects: 'projects/fetchProjects', addProject: 'projects/addProject'}),
        showDialog() {
            this.showModal = true
        },
        showEditDialog(project) {
            this.projectToBeEdited = project
            this.showEditModal = true
        },
        openNewsAdd() {
            this.showNewsAdd = true
        }
    },
    created() {
        this.fetchProjects()
    }
}
</script>

<style scoped>
  .news-container {
      max-height: 200px;
      overflow-y: auto;
      height: 200px;
      padding: 0 1rem 0 1rem;
  }

  .projects-container {
      overflow-y: auto;
  }

  .content-header {
      width: 100%;
      min-height: 4.3rem;
      font-size: 1.5rem;
      display: flex;
      align-items: center;
      justify-content: center;
  }

  .v-btn--fab:focus {
    outline: none !important;
  }
</style>

<style>
  .project-fab .v-speed-dial__list {
    padding-bottom: 0.5rem !important;
  }
</style>
