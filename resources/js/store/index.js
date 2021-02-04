import Vue from 'vue'
import Vuex from 'vuex'
import auth from './auth'
import error from './error'
import profile from './profile'
import message from './message'
import modal from './modal'
import group from './group'
import route from './route'

Vue.use(Vuex)

const store = new Vuex.Store({
  modules: {
    auth,
    profile,
    error,
    message,
    modal,
    group,
    route
  }
})

export default store
