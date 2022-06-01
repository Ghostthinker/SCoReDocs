import Vue from "vue"
import VueRouter from "vue-router"
import Media from "../views/Media"
import Projects from "../views/Projects"
import Project from "../views/Project"
import UserAdministration from "../views/UserAdministration"
import Profile from "../views/Profile"
import Template from "../views/Template"
import Imprint from "../views/Imprint"
import Privacy from "../views/Privacy"
import AssessmentOverview from "../views/AssessmentOverview"
import DataExport from "../views/DataExport"
import Archive from "../views/Archive"
import Settings from "../views/Settings"

Vue.use(VueRouter)

const router = [
    {
        name: 'Media',
        path: '/media',
        component: Media,
        children: Media.routes
    },
    {
        name: 'projects',
        path: '/',
        component: Projects
    },
    {
        name: 'project',
        path: '/project/:projectId',
        component: Project,
        props: true,
        meta: { layout: 'row'}
    },
    {
        name: 'user-administration',
        path: '/user-administration',
        component: UserAdministration,
        meta: { authorize: ['canAccessUserAdministration']}
    },
    {
        name: 'assessment-overview',
        path: '/assessment-overview',
        component: AssessmentOverview,
        meta: { authorize: ['canAccessAssessmentOverview']}
    },
    {
        name: 'profile',
        path: '/profile',
        component: Profile
    },
    {
        name: 'template',
        path: '/template',
        component: Template,
        meta: { authorize: ['canAccessTemplate']}
    },
    {
        name: 'archive',
        path: '/archive',
        component: Archive
    },
    {
        name: 'data-export',
        path: '/data-export',
        component: DataExport,
        meta: { authorize: ['canAccessDataExport']}
    },
    {
        name: 'imprint',
        path: '/imprint',
        component: Imprint
    },
    {
        name: 'privacy',
        path: '/privacy',
        component: Privacy
    },
	{
		name: 'settings',
		path: '/settings',
		component: Settings
	}
]

export default new VueRouter({mode: 'history', routes: router})
