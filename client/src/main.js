import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'

// spectre.css
import '@/assets/scss/spectre/spectre-exp.scss';
import '@/assets/scss/spectre/spectre-icons.scss';
import '@/assets/scss/spectre/spectre.scss';
import '@/assets/scss/style.scss';

import VueProgressBar from 'vue-progressbar';
Vue.use(VueProgressBar, {
  color: '#57b1ff',
  failedColor: 'f56c6c'
});

Vue.config.productionTip = false

export default new Vue({
  router,
  store,
  render: function (h) { return h(App) }
}).$mount('#app')
