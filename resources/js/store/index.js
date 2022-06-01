import Vuex from 'vuex'
import Vue from 'vue'

import sections from './modules/sections'
import notifications from './modules/notifications'
import user from './modules/user'
import messages from "./modules/messages"
import projects from "./modules/projects"
import activities from "./modules/activities"
import news from "./modules/news"

Vue.use(Vuex)


export default new Vuex.Store({
    modules: {
        sections,
        notifications,
        user,
        messages,
        projects,
        news,
        activities
    }
})
