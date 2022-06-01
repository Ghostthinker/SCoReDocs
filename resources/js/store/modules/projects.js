import axios from 'axios'

let projectsTemplatesPromise = null
let projectsArchivePromise = null
let projectsPromise = null
let projectsAssessmentDocsTemplate = null

const state = {
    project: [],
    projects: [],
    projectsMeta: null,
    projectTypes: {
        PROJECT: {
            value: 'Project',
            translated: 'Projekt'
        },
        TEMPLATE: {
            value: 'Template',
            translated: 'Vorlage'
        },
        ARCHIVED: {
            value: 'Archived',
            translated: 'Archiviertes Projekt'
        },
        ASSESSMENT_DOC: {
            value: 'AssessmentDoc',
            translated: 'Assessment Doc'
        },
        PROJECT_TEMPLATE: {
            value: 'Project Template',
            translated: 'Forschungsprojektvorlage'
        }
    }
}

const getters = {
    getProject: (state) => state.project,
    getProjects: (state) => state.projects.filter(p => p.type === state.projectTypes.PROJECT.value).sort((a, b) => (a.title > b.title) ? 1 : -1),
    getAssessmentDocTemplate: (state) => state.projects.filter(p => p.type === state.projectTypes.TEMPLATE.value),
    getArchivedProjects: (state) => state.projects.filter(p => p.type === state.projectTypes.ARCHIVED.value).sort((a, b) => (a.title > b.title) ? 1 : -1),
    getProjectTemplates: (state) => state.projects.filter(p => p.type === state.projectTypes.PROJECT_TEMPLATE.value).sort((a, b) => (a.title > b.title) ? 1 : -1),
    getAllProjects: (state) => state.projects.sort((a, b) => (a.title > b.title) ? 1 : -1),
    getProjectTypes: (state) => state.projectTypes,
    getProjectsMeta: (state) => state.projectsMeta,
    getMyWatchedProjects: (state) => state.projects.filter(project => project.isUserWatchingProject)
}

const actions = {
    async fetchProject({commit}, projectId) {
        return new Promise((resolve, reject) => {
            axios.get('/rest/project/' + projectId).then((response) => {
                const project = response.data
                commit('SET_PROJECT', project)
                resolve(project)
            }, (e) => {
                console.error(e)
                reject(e)
            })
        })
    },
    async fetchProjects({commit, getters}) {
        projectsPromise = new Promise((resolve, reject) => {
            if (getters.getProjects && getters.getProjects.length > 0) {
                resolve(getters.getProjects)
                return
            }
            if(projectsPromise) {
                return projectsPromise
            }
            axios.get('/rest/projects').then((response) => {
                fetchProjects(commit, resolve, response)
            }, (e) => {
                console.error(e)
                reject(e)
            })
        })
        return projectsPromise
    },
    async fetchArchivedProjects({commit, getters}) {
        projectsArchivePromise = new Promise((resolve, reject) => {
            if (getters.getArchivedProjects && getters.getArchivedProjects.length > 0) {
                resolve(getters.getArchivedProjects)
                return
            }
            if (projectsArchivePromise) {
                return projectsArchivePromise
            }
            axios.get('/rest/archivedProjects').then((response) => {
                fetchProjects(commit, resolve, response)
            }, (e) => {
                console.error(e)
                reject(e)
            })
        })
        return projectsArchivePromise
    },
    async fetchAssessmentDocsTemplate({commit, getters}) {
        projectsAssessmentDocsTemplate = new Promise((resolve, reject) => {
            if (getters.getAssessmentDocTemplate && getters.getAssessmentDocTemplate.length > 0) {
                resolve(getters.getAssessmentDocTemplate)
                return
            }
            if (projectsAssessmentDocsTemplate) {
                return projectsAssessmentDocsTemplate
            }
            axios.get('/rest/templates/getAssessmentDocTemplate').then((response) => {
                fetchProjects(commit, resolve, response)
            }, (e) => {
                console.error(e)
                reject(e)
            })
        })
        return projectsAssessmentDocsTemplate
    },
    async fetchProjectTemplates({commit, getters}) {
        projectsTemplatesPromise = new Promise((resolve, reject) => {
            if (getters.getProjectTemplates && getters.getProjectTemplates.length > 0) {
                resolve(getters.getProjectTemplates)
                return
            }
            if (projectsTemplatesPromise) {
                return projectsTemplatesPromise
            }
            axios.get('/rest/templates/getProjectTemplates').then((response) => {
                fetchProjects(commit, resolve, response)
            }, (e) => {
                console.error(e)
                reject(e)
            })
        })
        return projectsTemplatesPromise
    },
    async putProject({commit}, project) {
        return new Promise((resolve, reject) => {
            axios.put('/rest/project/' + project.id, project).then((response) => {
                resolve(response)
            }, (e) => {
                console.error(e)
                reject(e)
            })
        })
    },
    async postProject({commit}, project) {
        return new Promise((resolve, reject) => {
            axios.post('/rest/project', project).then((response) => {
                resolve(response)
            }, (e) => {
                console.error(e)
                reject(e)
            })
        })
    },
    duplicateProject({commit}, projectId) {
        return new Promise((resolve, reject) => {
            axios.get('/rest/project/' + projectId + '/duplicate').then((response) => {
                resolve(response)
            }, (e) => {
                console.error(e)
                reject(e.response)
            })
        })
    },
    toggleWatchProject({commit}, project) {
        return new Promise((resolve, reject) => {
            axios.get('/rest/project/' + project.id + '/toggleWatch').then((response) => {
                resolve(response)
                project.isUserWatchingProject = !project.isUserWatchingProject
                commit('UPDATE_PROJECT', project)
            }, (e) => {
                reject(e.response)
            })
        })
    },
    addProject({commit}, project) {
        commit('ADD_PROJECT', project)
    },
    updateProject({commit}, data) {
        commit('UPDATE_PROJECT', data)
    }
}

const mutations = {
    PUSH_PROJECTS: (state, data) => state.projects = [...new Map([...state.projects, ...data].map(item => [item['id'], item])).values()],
    SET_PROJECTS_META: (state, data) => state.projectsMeta = data,
    SET_PROJECT: (state, project) => state.project = project,
    UPDATE_PROJECT: (state, project) => {
        const oldProject = state.projects.filter(p => p.id === project.id)
        if(oldProject.length > 0) {
            state.projects.splice(state.projects.findIndex(el => el.id === project.id), 1, project)
        } else {
            state.projects.push(project)
        }
    },
    ADD_PROJECT: (state, project) => (state.projects.push(project))
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}

function fetchProjects(commit, resolve, response) {
    let projects = []
    const meta = response.data.meta

    response.data.projects.forEach((item) => {
        projects = [...projects, {
            id: item.id,
            title: item.title,
            description: item.description,
            type: item.type,
            isUserWatchingProject: item.is_user_watching_project,
            basicCourse: !!item.basic_course
        }]
    })
    commit('PUSH_PROJECTS', projects)
    commit('SET_PROJECTS_META', meta)
    resolve(projects)
}
