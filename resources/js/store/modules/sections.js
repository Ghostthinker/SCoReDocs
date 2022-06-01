import axios from 'axios'
import Section from "../../models/section"


const state = {
    sections: [],
    section: [],
    sectionStatus: [],
    allSectionsLoaded: false
}

const getters = {
    getSections: (state) => state.sections,
    getSection: (state) => state.section,
    getSectionById: (state) => (id) => state.sections.find(obj => obj.id == id),
    getSortedSections: (state) => state.sections.sort((a, b) => a.index - b.index),
    getSectionStatus: (state) => state.sectionStatus,
    isAllSectionsLoaded: (state) => state.allSectionsLoaded
}

const actions = {
    async fetchSections({commit, dispatch}, {projectId, paginate}) {
        return new Promise((resolve, reject) => {
            let url = '/rest/projects/' + projectId + '/sections';
            if (!paginate) {
                url += '?items=all'
            }
            axios.get(url).then((response) => {
                let sections = []
                let data = response.data.data || response.data
                data.forEach((item) => {
                    sections = [...sections, Section.create(item)]
                })
                commit('SET_SECTIONS', sections)

                if(!response.data.links || !response.data.links.next) {
                    commit('SET_ALL_SECTIONS_LOADED')
                }
                resolve(response)
            }, (e) => {
                console.error(e)
                reject(e)
            })
        })
    },
    async fetchSectionsByUrl({commit, dispatch}, url) {
        return new Promise((resolve, reject) => {
            axios.get(url).then((response) => {
                let sections = []
                response.data.data.forEach((item) => {
                    sections = [...sections, Section.create(item)]
                })
                commit('PUSH_SECTIONS', sections)

                if(response.data.links && !response.data.links.next) {
                    commit('SET_ALL_SECTIONS_LOADED')
                }
                resolve(response)
            }, (e) => {
                console.error(e)
                reject(e)
            })
        })
    },
    getTrashedSection({}, data) {
        return new Promise((resolve, reject) => {
            axios.get('/rest/projects/' + data.projectId + '/sections/' + data.sectionId + '/trashed').then((response) => {
                resolve(response)
            }, (e) => {
                console.error(e)
                reject(e)
            })
        })
    },
    getSection({}, data) {
        return new Promise((resolve, reject) => {
            axios.get('/rest/projects/' + data.projectId + '/sections/' + data.sectionId).then((response) => {
                resolve(response)
            }, (e) => {
                console.error(e)
                reject(e)
            })
        })
    },
    async fetchSectionStatus({commit}, section) {
        return new Promise((resolve, reject) => {
            axios.get('/rest/projects/' + section.projectId + '/sections/' + section.id + '/status').then((response) => {
                const sectionStatus = response.data
                commit('SET_SECTION_STATUS', sectionStatus)
                resolve(sectionStatus)
            }, (e) => {
                reject(e)
                console.error(e)
            })
        })
    },
    async fetchElementLinkCount({}, refId) {
        return new Promise(resolve => {
            axios.get('/rest/refs/' + refId + '/count').then((response) => {
                const linkCount = response.data
                resolve(linkCount)
            }, (e) => {
                resolve([])
                console.error(e)
            })
        })
    },
    insertSection({commit}, section) {
        commit('ADD_SECTION', section)
    },
    updateSection({commit}, section) {
        commit('UPDATE_SECTION', section)
    },
    removeSection({commit}, sectionId) {
        commit('REMOVE_SECTION', sectionId)
    },
    scrollToSection({commit}, sectionId) {
        let element = document.getElementById('Section-'+sectionId)
        if (element == null) {
            element = document.querySelector('[ref="' + sectionId + '"]')
        }
        if (element != null) {
            let domRect = element.getBoundingClientRect()
            window.scrollTo(0, domRect.top + window.scrollY)
        }
    },
    openSection({commit}, {projectId, sectionId}){
        return new Promise(resolve => {
            axios.get('/rest/projects/' + projectId + '/sections/' + sectionId + '/open').then((response) => {
                resolve(response)
            }, (e) => {
                resolve(e)
            })
        })
    },
    closeSection({commit}, {projectId, sectionId}){
        return new Promise(resolve => {
            axios.get('/rest/projects/' + projectId + '/sections/' + sectionId + '/close').then((response) => {
                resolve(response)
            }, (e) => {
                resolve(e)
            })
        })
    },
    resetSections({commit}){
        commit('RESET_SECTIONS')
    }
}

const mutations = {
    SET_SECTIONS: (state, data) => {
        state.sections = data
        state.allSectionsLoaded = false
    },
    ADD_SECTION: (state, section) => {
        const found = state.sections.find(sec => sec.id === section.id)
        if (typeof(found) !== 'undefined') {
            throw 'Section existiert bereits mit ID:' + section.id
        }
        state.sections.push(section)
    },
    UPDATE_SECTION: (state, section) => (state.sections.splice(state.sections.findIndex(el => el.id === section.id), 1, section)),
    REMOVE_SECTION: (state, sectionId) => (state.sections.splice(state.sections.findIndex(el => el.id === sectionId), 1)),
    SET_SECTION_STATUS: (state, sectionStatus) => state.sectionStatus = sectionStatus,
    PUSH_SECTIONS: (state, data) => state.sections = [...new Map([...state.sections, ...data].map(item => [item['id'], item])).values()],
    SET_ALL_SECTIONS_LOADED: (state) => state.allSectionsLoaded = true,
    RESET_SECTIONS: (state) => {
        state.sections = []
        state.allSectionsLoaded = false
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
