import axios from 'axios'
import News from "../../models/news"

const state = {
    singleNews: [],
    news: [],
    canEditNews: false,
    canCreateNews: false
}

const getters = {
    getSingleNews: (state) => state.singleNews,
    getNews: (state) => state.news.sort((a, b) => (a.updatedAtTimestamp > b.updatedAtTimestamp) ? -1 : 1),
    getCanEditPermission: (state) => state.canEditNews,
    getCanCreatePermission: (state) => state.canCreateNews,
}

const actions = {
    async fetchNews({commit}) {
        return new Promise((resolve, reject) => {
            axios.get('/rest/news').then((response) => {
                let news = []
                response.data.news.forEach((item) => {
                    news = [...news, News.create(item)]
                })
                commit('SET_NEWS', news)
                commit('SET_CAN_EDIT_NEWS_PERMISSION', response.data.can_edit_news)
                commit('SET_CAN_CREATE_NEWS_PERMISSION', response.data.can_create_news)
                resolve(news)
            }, (e) => {
                console.error(e)
                reject(e)
            })
        })
    },
    async postNews({commit}, news) {
        return new Promise((resolve, reject) => {
            axios.post('/rest/news', news).then((response) => {
                resolve(response)
            }, (e) => {
                console.error(e)
                reject(e)
            })
        })
    },
    async putNews({commit}, news) {
        return new Promise((resolve, reject) => {
            axios.put('/rest/news', news).then((response) => {
                resolve(response)
            }, (e) => {
                console.error(e)
                reject(e)
            })
        })
    },
    async deleteNews({commit}, news) {
        return new Promise((resolve, reject) => {
            axios.delete('/rest/news/' + news.id).then((response) => {
                resolve(response)
            }, (e) => {
                console.error(e)
                reject(e)
            })
        })
    },
    async readNews({commit}, news) {
        return new Promise((resolve, reject) => {
            axios.post('/rest/news/' + news.id + '/read').then((response) => {
                resolve(response)
            }, (e) => {
                console.error(e)
                reject(e)
            })
        })
    },
    pushNews({commit}, news) {
        commit('PUSH_NEWS', news)
    },
    updateNews({commit}, news) {
        commit('UPDATE_NEWS', news)
    },
    removeNews({commit}, news) {
        commit('REMOVE_NEWS', news)
    },
}

const mutations = {
    SET_NEWS: (state, data) => state.news = data,
    SET_CAN_EDIT_NEWS_PERMISSION: (state, data) => state.canEditNews = data,
    SET_CAN_CREATE_NEWS_PERMISSION: (state, data) => state.canCreateNews = data,
    PUSH_NEWS: (state, data) => state.news = [...new Map([...state.news, ...[data]].map(item => [item['id'], item])).values()],
    UPDATE_NEWS: (state, news) => {
        const oldNews = state.news.filter(p => p.id === news.id)
        if(oldNews.length > 0) {
            state.news.splice(state.news.findIndex(el => el.id === news.id), 1, news)
        } else {
            state.projects.push(news)
        }
    },
    REMOVE_NEWS: (state, newsId) => (state.news.splice(state.news.findIndex(el => el.id === newsId), 1)),
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
