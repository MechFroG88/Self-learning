export default {
  namespaced: true,
  state: {
    lessons: []
  },
  mutations: {
    SET_LESSONS(state, lessons) {
      state.lessons = lessons;
    },
    RESET_LESSONS(state) {
      state.lessons = [];
    }
  }
}