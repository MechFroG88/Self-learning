export default {
  namespaced: true,
  state: {
    lesson: {}
  },
  mutations: {
    SET_LESSON(state, lesson) {
      state.lesson = lesson;
    }
  }
}