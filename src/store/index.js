import Vue from 'vue'
import Vuex from 'vuex'
import axios from '../helper/axios'
import tokenUtils from '../helper/tokenUtils'
import router from '../router'

Vue.use(Vuex)
let baseURL = process.env.VUE_APP_API_URL

const store = new Vuex.Store({
  state: {
    user: {
        token: '',
        id: '',
        email: '',
        name: '',
        role: '',
        isLoggedIn: false
    }
  },
  mutations: {
    initialiseStore(state) {
        if (localStorage.getItem('store')) {
            this.replaceState(Object.assign(state, JSON.parse(localStorage.getItem('store'))));
        }
    },
    addWebToken: function(state, data) {
      state.user.token = data.jwt;
      const user = tokenUtils.decodeToken(data.jwt)
      state.user.id = user.data.id
      state.user.name = user.data.firstname
      state.user.role = user.data.role
      state.user.isLoggedIn = true;
    },
    removeWebToken: function(state) {
      state.user.token = '';
      state.user.id = '';
      state.user.email = '';
      state.user.name = '';
      state.user.role = '';
      state.user.isLoggedIn = false;
    }
  },
  actions: {
    login: function(context, userInput) {
        return new Promise((resolve, reject) => {
            axios.post(`${baseURL}/auth/login`, userInput).then(({data}) => {
                if (data.jwt !== '') {
                    context.commit('addWebToken', data);
                    router.push({name: 'Home'});
                }
            }).catch(error => {
                if (error.response.status === 403) {
                    resolve(403)
                }
                reject(error)
            })
        })
      
    },
    logout: function(context) {
      context.commit('removeWebToken');
      router.push({name: 'GoodBye'});
    }
  }
});

store.subscribe((mutation, state) => {
    localStorage.setItem('store', JSON.stringify(state));
})

export default store