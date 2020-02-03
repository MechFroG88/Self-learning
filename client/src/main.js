import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'

import '@/assets/scss/spectre/spectre-exp.scss';
import '@/assets/scss/spectre/spectre-icons.scss';
import '@/assets/scss/spectre/spectre.scss';
import '@/assets/scss/style.scss';

Vue.config.productionTip = false

new Vue({
  router,
  store,
  render: function (h) { return h(App) }
}).$mount('#app')
