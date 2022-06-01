<template>
    <div class="activity-container">
        <div v-if="isInProject" ref="activityFeed" class="section-select-container">
            <v-select
                ref="activitySelect"
                class="section-select"
                label="Filter"
                v-model="filterSectionId"
                :items="getSectionTitles"
                @change="loadActivitiesBySection(filterSectionId)"
                item-text="title"
                item-value="id"
                :attach="$refs.activityFeed"
                :menu-props="{ top: false, bottom: true }"
                bottom
            >
                <template v-slot:prepend-item>
                    <v-list-item
                        ripple
                        @click="resetFilter"
                    >
                        <v-list-item-content>
                            <v-list-item-title>
                                Filter zurücksetzen
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-divider class="mt-2"></v-divider>
                </template>
            </v-select>
        </div>
        <div id="activity-feed" class="activity-feed" >
            <div
                v-for="(activity, index) in getFilteredActivitiesFilteredBySection" :key="index"
            >
                <activity :is-in-project="isInProject" :activity="activity" :ref="'activity' + index"></activity>
            </div>
            <v-card v-if="isLoading" class="skeleton">
                <v-skeleton-loader
                    type="article"
                ></v-skeleton-loader>
            </v-card>
            <div v-if="getFilteredActivitiesFilteredBySection.length === 0 && !isLoading" class="no-data-container">
                Keine Aktivitäten
            </div>
        </div>
    </div>
</template>

<script>
import {createNamespacedHelpers} from 'vuex'
import Activity from "./Activity"

const {mapGetters, mapActions} = createNamespacedHelpers('activities')
export default {
    name: "ActivityFeed",
    components: {Activity},
    data() {
        return {
            currentLink: null,
            isLoading: false,
            loadedActivitiesCount: 0,
            filterSectionId: null
        }
    },
    props: {
        isInProject: {
            type: Boolean,
            default: false
        },
        projectId: {
            required: false
        }
    },
    computed: {
        ...mapGetters(['getActivities', 'getActivitiesNextLink', 'getProjectActivities']),
        getMyWatchedProjects() {
            return this.$store.getters['projects/getMyWatchedProjects']
        },
        getMyWatchedProjectsActivities() {
            return this.getActivities.filter((activity) => {
                return this.getMyWatchedProjects.filter(project => project.id === activity.projectId).length > 0
            })
        },
        getFilteredActivities() {
            if (this.isInProject) {
                return this.getProjectActivities(this.projectId)
            } else {
                return this.getMyWatchedProjectsActivities
            }
        },
        getSectionTitles() {
            return this.$store.getters['sections/getSortedSections'].map(e => {
                return {title: e.title, id: e.id}
            })
        },
        getFilteredActivitiesFilteredBySection() {
            if (this.filterSectionId) {
                return this.getFilteredActivities.filter(act => act.sectionId == this.filterSectionId)
            }
            return this.getFilteredActivities
        }
    },
    methods: {
        ...mapActions(['fetchActivities', 'fetchActivitiesPaginated', 'addNewActivity']),
        onScroll() {
            if (this.getActivitiesNextLink !== null) {
                if (this.currentLink === this.getActivitiesNextLink) {
                    return
                }

                if (this.loadedActivitiesCount !== this.getFilteredActivities.length) {
                    this.loadedActivitiesCount = this.getFilteredActivities.length
                    this.isLoading = false
                }

                let triggerActivity = this.$refs["activity" + (this.getFilteredActivities.length - 1)]
                if (triggerActivity) {
                    let marginTriggerActivityTop = triggerActivity[0].$el.getBoundingClientRect().top
                    let innerHeight = window.innerHeight
                    if ((marginTriggerActivityTop - innerHeight) < 250 && !this.isLoading) {
                        this.currentLink = this.getActivitiesNextLink
                        this.fetchActivitiesPaginated({url: this.getActivitiesNextLink})
                        this.isLoading = true
                    }
                }
            } else {
                this.isLoading = false
            }
        },
        initEcho() {
            Echo.channel('activity')
                .listen('.newActivity', (e) => {
                    let currentUserId = this.$store.getters['user/getUserId'];
                    if (currentUserId == e.activity.userId) {
                        e.activity.read = true
                    }
                    if (e.activity.targetUserIds.length > 0 &&
                        e.activity.targetUserIds.indexOf(currentUserId.toString()) < 0) {
                        return;
                    }
                    this.addNewActivity(e.activity)
                })
        },
        loadActivitiesBySection(sectionId) {
            this.isLoading = true
            this.fetchActivities({url: '/rest/activities/project/' + this.projectId + '/section/' + sectionId})
                .then(() => {
                    this.isLoading = false
                })
        },
        resetFilter() {
            this.$refs.activitySelect.blur()
            this.filterSectionId = null
            this.isLoading = true
            this.fetchActivities({url: '/rest/activities/project/' + this.projectId})
                .then(() => {
                    this.isLoading = false
                })
        }
    },
    created() {
        this.isLoading = true
        if (!this.isInProject) {
            this.fetchActivities({url: '/rest/activities'})
                .then(() => {
                    this.isLoading = false
                })
        } else {
            this.fetchActivities({url: '/rest/activities/project/' + this.projectId})
                .then(() => {
                    this.isLoading = false
                })
        }
    },
    mounted() {
        this.initEcho()
        this.$nextTick(function () {
            document.getElementById('activity-feed').addEventListener('scroll', this.onScroll)
            this.onScroll()
            this.loadedActivitiesCount = this.getActivities.length
        })
    },
    beforeDestroy() {
        Echo.leave('activity')

        if (document.getElementById('activity-feed')) {
            document.getElementById('activity-feed').removeEventListener('scroll', this.onScroll)
        }
    }
}
</script>

<style lang="scss" scoped>

.activity-container {
    display: flex;
    flex: 1;
    min-height: 0;
    flex-direction: column;
}

.activity-feed {
    overflow-y: auto;
    flex: 1
}

.section-select {
    flex-grow: 0;
    padding: 0 1rem 0.5rem 1rem;
}

.section-select-container {
    position: relative;
}

.skeleton {
    margin-right: 0.5rem;
    margin-left: 0.5rem;
}

.no-data-container {
    padding: 1rem;
}

.activity-container::v-deep .v-menu__content {
    top: 55px !important;
    background-color: white;
}
</style>
