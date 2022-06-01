<template>
    <default>
        <template v-slot:content-header>
            <span>Vorlagen</span>
        </template>
        <template v-slot:content-body>
            <div class="content-container">
                <h4 class="projects-header">Assessment</h4>
                <projects-grid
                    v-if="getAssessmentDocTemplate.length > 0"
                    v-bind:projects="getAssessmentDocTemplate"
                    :meta="getProjectsMeta"
                    :hideFooter="true"
                    @openProjectEditModal="showEditDialog($event)">
                    ></projects-grid>
                <h4 class="projects-header">Forschungsprojekt</h4>
                <projects-grid
                    v-if="getProjectTemplates.length > 0"
                    v-bind:projects="getProjectTemplates"
                    :meta="getProjectsMeta"
                    @openProjectEditModal="showEditDialog($event)">
                    ></projects-grid>
            </div>
            <project-form :inputData.sync="showModal"
                          :headline="'Vorlage erstellen'"
                          :request-u-r-i="'/rest/templates'"
                          :success-message="'Anlegen der Vorlage war erfolgreich'"
                          :generic-error-message="'Es gab einen Fehler beim Anlegen der Vorlage'"
                          :not-allowed-message="'Nur das SCoRe-Team und Lernbegleiter können eine Vorlage anlegen'"
                          @addProject="addProject"></project-form>
            <template v-if="showEditModal && projectToBeEdited">
                <projects-edit-form  :inputData.sync="showEditModal"
                                     :project="projectToBeEdited"
                                     :headline="'Forschungsprojektvorlage bearbeiten'"
                                     :success-message="'Das Bearbeiten der Forschungsprojektvorlage war erfolgreich'"
                                     :generic-error-message="'Es gab einen Fehler beim Bearbeiten der Forschungsprojektvorlage'"
                                     :not-allowed-message="'Nur das SCoRe-Team kann Forschungsprojektvorlagen bearbeiten'"
                ></projects-edit-form>
            </template>
        </template>
        <template v-slot:content-footer>
            <v-btn
                fab
                color="primary"
                v-on:click="showDialog()"
                v-if="getAssessmentDocTemplate.length === 0"
            >
                <v-icon>mdi-plus</v-icon>
            </v-btn>
        </template>
    </default>
</template>
<script>
import ProjectsGrid from "../components/projects/ProjectsGrid"
import ProjectForm from "../components/projects/ProjectsForm"
import ProjectsEditForm from "../components/projects/ProjectsEditForm"
import {createNamespacedHelpers} from 'vuex'
import Default from "../layouts/Default"

const {mapGetters, mapActions} = createNamespacedHelpers('projects')

export default {
    name: "Template",
    components: {Default, ProjectForm, ProjectsGrid, ProjectsEditForm},
    data() {
        return {
            showModal: false,
            showEditModal: false,
            projectToBeEdited: null,
            version1: Object,
            version2: Object,
        }
    },
    computed: {
        ...mapGetters(['getAssessmentDocTemplate', 'getProjectsMeta', 'getProjectTypes', 'getProjectTemplates']),
    },
    methods: {
        ...mapActions(['fetchProjectTemplates', 'fetchAssessmentDocsTemplate', 'addProject']),
        showDialog() {
            this.showModal = true
        },
        showEditDialog(project) {
            this.showEditModal = true
            this.projectToBeEdited = project
        }
    },
    created() {
        this.fetchAssessmentDocsTemplate().then(e => {
            this.fetchProjectTemplates()
        })
    }
}
</script>

<style scoped>
.projects-header {
    padding-left: 2rem
}
.content-container {
    text-align: left;
}
</style>
