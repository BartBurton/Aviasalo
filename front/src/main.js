import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import axios from 'axios'
import vuetify from './plugins/vuetify'
import authenticator from './plugins/authanticator'


Vue.config.productionTip = false

axios.defaults.baseURL = store.getters.api
Vue.prototype.$axios = axios


const moment = require('moment')
require('moment/locale/ru')

Vue.use(require('vue-moment'), {
  moment
})

authenticator.setStore().then(e => {
  new Vue({
    router,
    store,
    vuetify,
    render: function (h) {
      return h(App)
    }
  }).$mount('#app')
})
