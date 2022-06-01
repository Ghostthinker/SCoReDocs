<template>
    <v-list class="project-outline">
        <template v-for="section in sections">
            <v-divider :key="'divider'+section.id" v-if="section.heading < 2"></v-divider>
            <ProjectOutlineElement :key="section.id" :section="section"></ProjectOutlineElement>
        </template>
    </v-list>
</template>

<script>
import ProjectOutlineElement from "./ProjectOutlineElement"
import {createNamespacedHelpers} from "vuex"
const {mapActions} = createNamespacedHelpers('activities')

export default {
    name: "ProjectOutline",
    props: {
        sections: {
            type: Array,
            required: true
        },
        projectId: {
            required: true
        }
    },
    components: {
        ProjectOutlineElement
    },
    data() {
        return {
            panel: 0,
        }
    },
    computed: {
        collapsed() {
            return this.panel === 0
        }
    },
    methods: {
        ...mapActions(['fetchUnreadActivitiesCounts', 'updateActivitiesCount']),
        formattedSectionHeadingClass(section) {
            return 'pl-' + (section.heading > 1 ? section.heading * 10 : section.heading)
        }
    },
    created() {
        this.fetchUnreadActivitiesCounts(this.projectId)
        Echo.channel('activitycount.' + this.projectId)
            .listen('.newActivityCount', (e) => {
                this.updateActivitiesCount({
                    sectionId: e.sectionId,
                    activityCountDiff: 1
                })
            })
    },
    beforeDestroy() {
        Echo.leave('activitycount.' + this.projectId)
    }
}
</script>

<style lang="scss" scoped>
    .project-outline  {
        padding-left: 1em;
        padding-right: 1em;
    }
</style>

<style lang="scss">
    .v-list-item {
        min-height: 30px !important;
    }

    .v-divider {
        margin-top: 0rem;
        margin-bottom: 0.25rem;
    }
</style>
