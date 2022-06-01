<template>
    <div class="grid-row">
        <div v-for="project in projects"
             :key="project.id" class="grid-item">
            <projects-card
                v-bind:project="project"
                :meta="meta"
                @openProjectEditModal="$emit('openProjectEditModal', $event)"
            ></projects-card>
        </div>
    </div>
</template>

<script>
import ProjectsCard from './ProjectsCard'

export default {
    name: 'ProjectsGrid',
    components: {ProjectsCard: ProjectsCard},
    props: {
        projects: {
            default: [],
            required: true
        },
        meta: {
            type: Object
        }
    }
}
</script>

<style lang="scss" scoped>
.grid-row {
    display: flex;
    flex-flow: row wrap;
    justify-content: center;
}

.grid-item {
    flex-basis: 20%;
    -ms-flex: auto;
    position: relative;
    box-sizing: border-box;
    flex-grow: 1;
    max-width: 350px;
}
</style>
