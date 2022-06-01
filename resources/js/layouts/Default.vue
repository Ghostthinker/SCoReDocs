<template>
    <div class="default-layout">
        <v-card v-if="!getLeftMenuCollapseState" class="menus left-menu">
            <div class="left-menu-header">
                <div class="menu-icon-container-left">
                    <v-btn icon @click="toggleMenuCollapsedState('left-menu')"><v-icon large>mdi-close</v-icon></v-btn>
                </div>
                <div class="menu-logo-container">
                    <router-link :to="{ name: 'projects' }">
                        <img src="/assets/images/score-logo.png" alt="score" style="height: 40px;">
                    </router-link>
                </div>
            </div>
            <v-divider></v-divider>
            <div class="left-menu-content-title">
                <slot name="left-menu-title">Projekte</slot>
            </div>
            <div class="left-menu-content" >
                <slot name="left-menu">
                    <projects-list></projects-list>
                </slot>
            </div>
            <v-divider></v-divider>
            <div class="left-menu-footer">
                <default-score-footer></default-score-footer>
            </div>
        </v-card>
        <v-card v-else class="menus left-menu collapsed">
            <div class="left-menu-header">
                <div class="menu-icon-container">
                    <v-btn icon @click="toggleMenuCollapsedState('left-menu')"><v-icon large>mdi-menu</v-icon></v-btn>
                </div>
                <div class="menu-logo-container">
                    <router-link :to="{ name: 'projects' }">
                        <img src="/assets/images/score_logo_icon_only.png" alt="score" style="height: 40px;">
                    </router-link>
                </div>
            </div>
        </v-card>
        <div class="content-container">
            <div id="content"  class="content">
                <div class="content-header" :class="getContentWidth">
                    <slot name="content-header"></slot>
                </div>
                <div class="content-body" :class="[getContentBodyRow, getContentWidth]">
                    <slot name="content-body"></slot>
                </div>
            </div>
            <div class="content-fab">
                <slot name="content-footer"></slot>
            </div>
            <div class="notifications">
                <notification></notification>
                <slot name="mentioning"></slot>
            </div>
        </div>
        <v-card v-if="!getRightMenuCollapseState" class="menus right-menu">
            <div class="right-menu-header">
                <v-btn icon @click="toggleMenuCollapsedState('right-menu')"><v-icon large>mdi-chevron-right</v-icon></v-btn>
                <v-avatar size="30">
                    <img :src="avatarUrl" v-if="avatarUrl" alt="profile-img">
                    <v-icon color="primary" v-else large>mdi-account-circle</v-icon>
                </v-avatar>
                <div class="user-name-container">
                    <span v-if="getProfile" :title="getProfile.name">
                        {{getProfile.name}}
                    </span>
                </div>
                <help :useIcon="true"></help>
                <v-btn icon @click="logout">
                    <v-icon style="font-size: 30px" color="primary">mdi-logout</v-icon>
                </v-btn>
            </div>
            <v-divider></v-divider>
            <navigation-menu></navigation-menu>
            <slot name="right-menu-heading">
                <v-divider></v-divider>
                <div class="right-menu-heading">
                    Aktivitäten
                </div>
            </slot>
            <slot name="right-menu">
                <activity-feed></activity-feed>
            </slot>
        </v-card>
        <v-card v-if="getRightMenuCollapseState" class="menus right-menu collapsed">
            <div class="left-menu-header" @click="toggleMenuCollapsedState('right-menu')">
                <div class="menu-icon">
                    <v-btn icon><v-icon large>mdi-chevron-left</v-icon></v-btn>
                </div>
                <v-avatar size="30">
                    <img :src="avatarUrl" v-if="avatarUrl" alt="profile-img">
                    <v-icon color="primary" v-else large>mdi-account-circle</v-icon>
                </v-avatar>
            </div>
        </v-card>
    </div>
</template>

<script>
import {createNamespacedHelpers} from 'vuex'
import ProjectsList from "../components/projects/ProjectsList"
import NavigationMenu from "../components/navigation/NavigationMenu"
import DefaultScoreFooter from "../components/footer/DefaultScoreFooter"
import ActivityFeed from "../components/activities/ActivityFeed"
import Project from "../models/project"
const {mapGetters, mapActions} = createNamespacedHelpers('user')
export default {
    name: "Default",
    components: {
        ActivityFeed,
        DefaultScoreFooter,
        NavigationMenu,
        ProjectsList
    },
    methods: {
        ...mapActions(['fetchUser', 'fetchProfile', 'logout', 'toggleMenuCollapsedState']),
        initEcho() {
            Echo.channel('project')
                .listen('.newProject', (item) => {
                    const project = Project.create(item.project)
                    this.$store.dispatch('projects/addProject', project)
                }).listen('.updateProject', (item) => {
                    const project = Project.create(item.project)
                    this.$store.dispatch('projects/updateProject', project)
                })
        }
    },
    computed: {
        ...mapGetters(['getUser', 'getProfile', 'getLeftMenuCollapseState', 'getRightMenuCollapseState']),
        avatarUrl(){
            if(this.getProfile &&  this.getProfile.avatar){
                return this.getProfile.avatar
            } else{
                return false
            }
        },
        getContentBodyRow() {
            const isRowLayout = this.$route.meta && this.$route.meta.layout === 'row'
            return {
                'content-body-row': isRowLayout
            }
        },
        getContentWidth() {
            const isRowLayout = this.$route.meta && this.$route.meta.layout === 'row'
            return {
                'content-width': isRowLayout
            }
        },
    },
    mounted() {
        this.initEcho()
        const userPromise = this.fetchUser()
        this.fetchProfile()
        this.$store.dispatch('projects/fetchProjects')
        this.$store.dispatch('projects/fetchArchivedProjects')
        userPromise.then(user => {
            if(user.meta.canAccessTemplate) {
                this.$store.dispatch('projects/fetchProjectTemplates')
            }
        })
    },
    beforeDestroy() {
        Echo.leaveChannel('project')
    }
}
</script>

<style scoped lang="scss">
    .default-layout {
        height: 100%;
        display: flex;
        justify-content: space-between;

        .menus {
            min-width: 360px;
            width: 360px;
            display: flex;
        }

        .menu-icon-container-left {
            font-size: 32px;
            flex-grow: 0;
        }

        .menu-icon-container-right {
            font-size: 32px;
            flex-grow: 0;
        }

        .menu-logo-container {
            margin-right: 2rem;
            flex-grow: 1;
            display: flex;
        }

        .right-menu {
            display: flex;
            flex-direction: column;
            max-height: 100vh;
            z-index: 100;
        }

        .right-menu-header {
            display: flex;
            width: 100%;
            align-items: center;
            height: 4rem;
        }

        .right-menu-heading {
            padding: 1rem 1rem 0.75rem 1rem;
            font-size: 1.25em;
        }

        .left-menu {
            display: flex;
            flex-direction: column;
            max-height: 100vh;
            z-index: 100;
        }

        .left-menu-header {
            display: flex;
            width: 100%;
            align-items: center;
            height: 4rem;
            padding-left: 10px;
            cursor: pointer;
            flex-shrink: 0;
        }

        .left-menu-content-title {
            font-size: 1.25em;
            padding: 1rem;
            padding-bottom: 0.5em;
            font-weight: bold;
        }

        .left-menu-content {
            overflow-y: auto;
            flex-grow: 1;
        }

        .left-menu-footer {
            display: flex;
            flex-flow: column;
            padding:  1rem;
            font-size: 1em;
        }

        .user-name-container {
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            flex-grow: 1;
            margin-left: 5px;
        }

        .collapsed {
            min-width: 120px;
            width: 10%;
            background: transparent;
            box-shadow: none;
        }

        @media only screen and (max-width:720px) {
            .collapsed {
                min-width: 60px;
                .menu-logo-container,
                .v-avatar {
                    display: none;
                }
            }
        }

        .content-header {
            width: 100%;
            min-height: 4.3rem;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .content-fab {
            position: absolute;
            right: 0;
            bottom: 1rem;
            z-index: 180;
        }

        .content-body {
            overflow-y: auto;
            overflow-x: hidden;
            max-height: calc(100vh - 4em);
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            max-width: 100%;
        }

        .content {
            display: flex;
            flex-direction: column;
            overflow-x: auto;
            overflow-y: auto;
            position: relative;
            width: 100%;
            height: 100%;
        }

        .content-container {
            flex-shrink: 1;
            flex-grow: 1;
            overflow: hidden;
            height: 100vh;
            max-height: 100vh;
            position: relative;
        }

        .content-body-row{
            display: flex;
            width: 100%;
            justify-content: center;
            flex-direction: row;
            scrollbar-width: none;  /* Firefox */
        }

        .content-width{
            min-width: 1200px;
        }

        .content-body-row::-webkit-scrollbar {
            width: 0;
        }

        .notifications {
            position: absolute;
            z-index: 170;
            right: 0;
            top: .5rem;
            width: calc(360px + 1rem);
        }
}
</style>
