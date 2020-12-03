import Vue from 'vue'
import Vuex from 'vuex'
import axios from '../helper/axios'
import tokenUtils from '../helper/tokenUtils'
import router from '../router'

Vue.use(Vuex)

const store = new Vuex.Store({
  state: {
    user: {
        access_token: '',
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
    setUser: function(state, user) {
      state.user.id = user.data.id
      state.user.name = user.data.firstname
      state.user.role = user.data.role
      state.user.isLoggedIn = true;
    },
    addWebToken: function(state, token) {
      state.user.access_token = token;
    },
    removeWebToken: function(state) {
      state.user.access_token = '';
      state.user.id = '';
      state.user.email = '';
      state.user.name = '';
      state.user.role = '';
      state.user.isLoggedIn = false;
      localStorage.removeItem('refresh');
    },
    addRefreshToken: function(token) {
      localStorage.setItem('refresh', token);
    }
  },
  actions: {
    login: function(context, userInput) {
        return new Promise((resolve, reject) => {
            axios.post(`/auth/login`, userInput).then(({data}) => {
                if (data.jwt !== '') {
                  const access_token = data.jwt.access_token;
                  const refresh_token = data.jwt.refresh_token;
                  const user = tokenUtils.decodeToken(access_token);
                  context.commit('addWebToken', access_token);
                  context.commit('addRefreshToken', refresh_token);
                  context.commit('setUser', user);
                  router.push({name: 'Home'});
                }
            }).catch(error => {
                if (error.response && error.response.status === 403) {
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