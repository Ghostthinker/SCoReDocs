<template>
    <div>
        <div v-for="project in getProjects"
             :key="project.id">
            <projects-card
                v-bind:project="project"
                :meta="getProjectsMeta"
                @openProjectEditModal="showEditDialog($event)">
                ></projects-card>
        </div>
        <div v-if=showEditModal>
            <projects-edit-form  :inputData.sync="showEditModal"
                                 :project="projectToBeEdited"
                                 :headline="'Forschungsprojekt bearbeiten'"
                                 :success-message="'Das Bearbeiten des Projektes war erfolgreich'"
                                 :generic-error-message="'Es gab einen Fehler beim Bearbeiten des Projektes'"
                                 :not-allowed-message="'Nur das SCoRe-Team kann Projekte bearbeiten'"
            ></projects-edit-form>
        </div>
    </div>
</template>

<script>
import ProjectsCard from './ProjectsCard'
import ProjectsEditForm from "./ProjectsEditForm"
import {createNamespacedHelpers} from 'vuex'

const {mapGetters, mapActions} = createNamespacedHelpers('projects')
export default {
    name: 'ProjectsList',
    components: {ProjectsCard: ProjectsCard, ProjectsEditForm},
    data() {
        return {
            showEditModal: false,
            projectToBeEdited: null
        }
    },
    computed: {
        ...mapGetters(['getProjectsMeta', 'getProjects']),
    },
    methods: {
        ...mapActions(['fetchArchivedProjects', 'updateProject']),
        showEditDialog(project) {
            this.projectToBeEdited = project
            this.showEditModal = true
        },
    }
}
</script>

<style lang="scss" scoped>

</style>
