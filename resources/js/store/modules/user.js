import axios from 'axios'
import User from "../../models/user"

let userPromise = null

const state = {
    profile: null,
    user: null
}

const getters = {
    getProfile: (state) => state.profile,
    getUser: (state) => state.user,
    getUserId: (state) => state.user ? state.user.id : null,
    getLeftMenuCollapseState: (state) => state.user ? state.user.meta.leftMenuCollapsed : 0,
    getRightMenuCollapseState: (state) => state.user ? state.user.meta.rightMenuCollapsed : 0
}

const actions = {
    async fetchProfile({ commit }){
        return new Promise((resolve, reject)  => {
            if (state.profile) {
                resolve(state.profile)
                return
            }
            axios.get('/rest/profile').then((response) => {
                commit('SET_PROFILE', response.data)
                resolve(response)
            }, (e) => {
                reject(e)
                console.error(e)
            })
        })
    },
    async fetchUser({ commit, state}){
        userPromise = new Promise((resolve, reject)  => {
            if (state.user) {
                resolve(state.user)
                return
            }
            if(userPromise) {
                return userPromise
            }
            axios.get('/rest/user').then((response) => {
                commit('SET_USER', response.data)
                resolve(response.data)
            }, (e) => {
                reject(e)
                console.error(e)
            })
        })
        return userPromise
    },
    logout({commit}) {
        return new Promise((resolve, reject)  => {
            axios.post('/logout').then((response) => {
                document.location.href = "/login"
                resolve(response)
            }, (e) => {
                reject(e)
                console.error(e)
            })
        })
    },
    // eslint-disable-next-line no-unused-vars
    updateProfile({commit}, formData) {
        return axios.post('/rest/profile', formData)
    },
    markIntroVideoAsSeen({commit}) {
        return axios.get('/rest/user/markIntroVideoAsSeen').then(() => {
            commit('SET_USER_HAS_SEEN_INTRO_VIDEO')
        })
    },
    async toggleMenuCollapsedState({commit}, menu) {
        if(menu === 'left-menu') {
            commit('SET_USER_LEFT_MENU_STATE', !state.user.meta.leftMenuCollapsed)
        } else {
            commit('SET_USER_RIGHT_MENU_STATE', !state.user.meta.rightMenuCollapsed)
        }
        return new Promise((resolve, reject) => {
            axios.get('/rest/user/toggleMenuCollapseState/'+ menu).then((response) => {
                resolve(response)
            }, (e) => {
                console.error(e)
                reject(e)
            })
        })
    }
}

const mutations = {
    SET_PROFILE: (state, profile) => state.profile = profile,
    SET_USER: (state, user) => state.user = User.create(user),
    SET_USER_HAS_SEEN_INTRO_VIDEO: (state) => state.user.meta.hasSeenIntroVideo = 1,
    SET_USER_LEFT_MENU_STATE: (state, collapseState) => state.user.meta.leftMenuCollapsed = collapseState,
    SET_USER_RIGHT_MENU_STATE: (state, collapseState) => state.user.meta.rightMenuCollapsed = collapseState,
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
