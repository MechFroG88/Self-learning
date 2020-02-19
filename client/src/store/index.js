import Vue from 'vue'
import Vuex from 'vuex'
import createPersistedState from 'vuex-persistedstate'

import user from './modules/user';
import lessons from './modules/lessons';
import admin_edit from './modules/admin_edit';
import admin_lessons from './modules/admin_lessons';

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    user,
    lessons,
    admin_edit,
    admin_lessons
  },
  state: {
  },
  mutations: {
  },
  actions: {
  },
  plugins: [createPersistedState()]
})
