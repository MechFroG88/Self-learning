export default {
  namespaced: true,
  state: {
    lessons: [],
    users: [],
  },
  mutations: {
    SET_LESSONS(state, lessons) {
      state.lessons = lessons;
    },
    RESET_LESSONS(state) {
      state.lessons = [];
    },
    SET_USERS(state, users) {
      state.users = users;
    },
    RESET_USERS(state) {
      state.users = [];
    }
  }
}