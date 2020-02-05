export default {
  namespaced: true,
  state: {
    selected_lesson_id: [0,0,0,0],
    selected_lesson_name: ['','','','']
  },
  mutations: {
    SELECT_LESSON(state, {ind, id, name}) {
      state.selected_lesson_id[ind] = id;
      state.selected_lesson_name[ind] = name;
    }
  }
}