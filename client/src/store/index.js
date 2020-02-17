import Vue from 'vue'
import Vuex from 'vuex'
import createPersistedState from 'vuex-persistedstate'

import lessons from './modules/lessons';
import admin_edit from './modules/admin_edit';

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    lessons,
    admin_edit,
  },
  state: {
  },
  mutations: {
  },
  actions: {
  },
  plugins: [createPersistedState()]
})
