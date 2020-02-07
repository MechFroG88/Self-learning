import Vue from 'vue'
import Vuex from 'vuex'
import createPersistedState from 'vuex-persistedstate'

import lessons from './modules/lessons';

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    lessons
  },
  state: {
  },
  mutations: {
  },
  actions: {
  },
  plugins: [createPersistedState()]
})
