function initialState() {
  return {
    selected_lesson_id: [0,0,0,0],
    selected_lesson_name: ['','','','']
  };
}

export default {
  namespaced: true,
  state: initialState(),
  mutations: {
    SELECT_LESSON(state, {ind, id, name}) {
      state.selected_lesson_id[ind] = id;
      state.selected_lesson_name[ind] = name;
    },
    RESET(state) {
      const s = initialState();
      Object.keys(s).forEach(key => state[key] = s[key]);
    }
  }
}