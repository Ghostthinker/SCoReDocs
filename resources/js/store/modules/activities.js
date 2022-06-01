import axios from 'axios'
import Activity from "../../models/activity"

const state = {
    activities: [],
    activitiesNextLink: [],
    activitiesCountList: [],
}

const getters = {
    getActivities: (state) => state.activities.sort((a, b) => (a.id < b.id) ? 1 : -1),
    getProjectActivities: (state) => (id) => { return state.activities.filter(p => p.projectId == id).sort((a, b) => (a.id < b.id) ? 1 : -1)},
    getActivitiesNextLink: (state) => state.activitiesNextLink,
    getActivityById: (state) => (id) => state.activities.find(obj => obj.id == id),
    getActivitiesCountList: (state) => state.activitiesCountList,
    getActivitiesCount: (state) => (sectionId) => {
        let found = state.activitiesCountList.find((el) => {
            return (sectionId && el.sectionId == sectionId)
        })
        if (!found) {
            return 0
        } else {
            return found.activitiesCount
        }
    },
}

const actions = {
    async fetchActivities({commit}, {url}) {
        return new Promise((resolve, reject) => {
            axios.get(url).then((response) => {
                let activities = []
                response.data.data.forEach((item) => {
                    activities = [...activities, Activity.create(item)]
                })
                commit('SET_ACTIVITIES', {activities : activities, links: response.data.links.next})
                resolve(response)
            }, (e) => {
                reject(e)
            })
        })
    },
    async fetchActivitiesPaginated({commit}, {url}) {
        return new Promise((resolve, reject) => {
            axios.get(url).then((response) => {
                let activities = []
                response.data.data.forEach((item) => {
                    activities = [...activities, Activity.create(item)]
                })
                commit('PUSH_ACTIVITIES', {activities : activities, links: response.data.links.next})
                resolve(response)
            }, (e) => {
                reject(e)
            })
        })
    },
    async fetchUnreadActivitiesCounts({commit}, projectId) {
        return new Promise((resolve, reject) => {
            axios.get('/rest/activity/unreadActivitiesCount/' + projectId).then((response) => {
                let unreadActivitiesCount = []
                response.data.forEach((item) => {
                    unreadActivitiesCount = [...unreadActivitiesCount, {
                        sectionId: item.section_id,
                        activitiesCount: item.activities_count
                    }]
                })
                commit('SET_ACTIVITY_COUNT', unreadActivitiesCount)
                resolve(response)
            }, e => {
                reject(e)
            })
        })
    },
    async markAllActivitiesAsRead({commit}, projectId) {
        return new Promise((resolve, reject) => {
            axios.get('/rest/activity/markAllActivitiesAsRead/' + projectId).then((response) => {
                commit('MARK_ALL_ACTIVITIES_AS_READ')
                resolve(response)
            },e => {
                reject(e)
            })
        })
    },
    addNewActivity({commit}, activity) {
        commit('UNSHIFT_ACTIVITY', Activity.create(activity))
    },
    async markAsRead({commit, getters}, activityId) {
        let activity = getters.getActivityById(activityId)
        activity.read = true
        commit('UPDATE_ACTIVITY', activity)
        return new Promise((resolve, reject) => {
            axios.get('/rest/activity/' + activityId + '/markAsRead').then((response) => {
                resolve(response)
            }, (e) => {
                reject(e)
            })
        })
    },
    updateActivitiesCount({commit}, data) {
        commit('UPDATE_ACTIVITY_COUNT', data)
    }
}

const mutations = {
     SET_ACTIVITIES: (state, data) => {
        state.activities = data.activities
        state.activitiesNextLink = data.links
    },
    PUSH_ACTIVITIES: (state, data) => {
        state.activities.push(...data.activities)
        state.activitiesNextLink = data.links
    },
    UNSHIFT_ACTIVITY: (state, activity) => {
         state.activities.unshift(activity)
    },
    UPDATE_ACTIVITY: (state, activity) => (state.activities.splice(state.activities.findIndex(el => el.id === activity.id), 1, activity)),
    SET_ACTIVITY_COUNT: (state, data) => {
         state.activitiesCountList = data
    },
    UPDATE_ACTIVITY_COUNT: (state, data) => {
        let found = state.activitiesCountList.find((el) => {
            return el.sectionId == data.sectionId
        })
        if (!found) {
            state.activitiesCountList.push({
                sectionId: data.sectionId,
                activitiesCount: 1,
            })
        } else {
            found.activitiesCount += data.activityCountDiff
        }
    },
    MARK_ALL_ACTIVITIES_AS_READ: (state) => {
        state.activities.map(item => item.read = true)
        state.activitiesCountList.map(item => item.activitiesCount = 0)
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
