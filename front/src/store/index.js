import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    storage: 'http://127.0.0.1:8000',
    api: 'http://127.0.0.1:8000/api',
    user: null,
  },
  mutations: {
    setUser(state, user){
      state.user = {...user}
    }
  },
  actions: {
  },
  modules: {
  },
  getters:{
    api: state => state.api,
    storage: state => state.storage,
    user: state => state.user
  }
})
