let initialState = () => ({
  id: 0,
  cn_name: '',
  class: '',
  lessons: [],
  forced_lessons: []
});

export default {
  namespaced: true,
  state: {
    user: initialState(),
  },
  mutations: {
    LOGIN(state, user) {
      for (let keys of Object.keys(user)) {
        state.user[keys] = user[keys];
      }
    },
    LOGOUT(state) {
      let s = initialState();
      Object.keys(s).forEach(el => state.user[el] = s[el]);
    }
  }
}