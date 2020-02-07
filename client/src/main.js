import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'

// spectre.css
import '@/assets/scss/spectre/spectre-exp.scss';
import '@/assets/scss/spectre/spectre-icons.scss';
import '@/assets/scss/spectre/spectre.scss';
import '@/assets/scss/style.scss';

// prgress bar
import VueProgressBar from 'vue-progressbar';
Vue.use(VueProgressBar, {
  color: '#57b1ff',
  failedColor: 'f56c6c'
});

import { ValidationProvider, extend, localize } from 'vee-validate';
import { required, regex, numeric } from 'vee-validate/dist/rules';
import zh_CN from 'vee-validate/dist/locale/zh_CN.json';
extend('required', required);
extend('regex', regex);
extend('numeric', numeric);
localize('zh_CN', zh_CN);
Vue.component('ValidationProvider', ValidationProvider);

Vue.config.productionTip = false

export default new Vue({
  router,
  store,
  render: function (h) { return h(App) }
}).$mount('#app')
