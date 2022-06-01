<template>
    <default>
        <template v-slot:content-header>
            <span>Archiv</span>
        </template>
        <template v-slot:content-body>
            <projects-grid
                v-if="getArchivedProjects.length > 0"
                v-bind:projects="getArchivedProjects"
                :meta="getProjectsMeta"
                @openProjectEditModal="showEditDialog($event)">
                ></projects-grid>
            <div v-if=showEditModal>
                <projects-edit-form  :inputData.sync="showEditModal"
                                     :project="projectToBeEdited"
                                     :headline="'Archiviertes Projekt bearbeiten'"
                                     :success-message="'Das Bearbeiten des archivierten Projektes war erfolgreich'"
                                     :generic-error-message="'Es gab einen Fehler beim Bearbeiten des archivierten Projektes'"
                                     :not-allowed-message="'Nur das SCoRe-Team kann archivierte Projekte bearbeiten'"
                ></projects-edit-form>
            </div>
        </template>
    </default>
</template>

<script>

import ProjectsGrid from "../components/projects/ProjectsGrid"
import {createNamespacedHelpers} from 'vuex'
import ProjectsEditForm from "../components/projects/ProjectsEditForm"
import Default from "../layouts/Default"

const {mapGetters, mapActions} = createNamespacedHelpers('projects')

export default {
    name: "Archive",
    components: {ProjectsGrid, ProjectsEditForm, Default},
    data() {
        return {
            showEditModal: false,
            projectToBeEdited: null
        }
    },
    computed: {
        ...mapGetters(['getArchivedProjects', 'getProjectTypes', 'getProjectsMeta']),
    },
    methods: {
        ...mapActions(['fetchArchivedProjects']),
        showEditDialog(project) {
            this.showEditModal = true
            this.projectToBeEdited = project
        }
    },
    created() {
        this.fetchArchivedProjects()
    }
}
</script>

<style scoped>

</style>
