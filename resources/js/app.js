require('./bootstrap')
import Vue from 'vue'
import Buefy from 'buefy'
import $ from 'jquery';
import VueTrumbowyg from 'vue-trumbowyg';
import VueApexCharts from 'vue-apexcharts'
import Trend from "vuetrend"
import 'buefy/dist/buefy.css'
import '@mdi/font/css/materialdesignicons.css'
import 'trumbowyg/dist/ui/trumbowyg.css';
import Index from './views/Index'
import router from './router'
import store from './store'

Vue.config.productionTip = false;

// Set Vue globally
window.Vue = Vue
window.$ = window.jQuery = $;

Vue.use(Buefy)
Vue.use(VueTrumbowyg);
Vue.use(VueApexCharts)
Vue.use(Trend)

Vue.component('apexchart', VueApexCharts)

//axios.defaults.baseURL = `${process.env.MIX_APP_URL}/api`
axios.defaults.baseURL = 'http://diary.test/api'

new Vue({
  router,
  store,
  created () {
    const userInfo = localStorage.getItem('user')
    if (userInfo) {
      const userData = JSON.parse(userInfo)
      this.$store.commit('setUserData', userData)
    }
    axios.interceptors.response.use(
      response => response,
      error => {
        if (error.response.status === 401) {
          this.$store.dispatch('logout')
        }
        return Promise.reject(error)
      }
    )
  },
  render: h => h(Index)
}).$mount('#app')
