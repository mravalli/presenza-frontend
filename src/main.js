import Vue from 'vue'
import Vuex from 'vuex'
import VueFilterDateFormat from '@vuejs-community/vue-filter-date-format'
import axios from 'axios'
import lodash from 'lodash'
import App from './App.vue'
import router from './router'
import Buefy from 'buefy'
import wysiwyg from "vue-wysiwyg"
import 'buefy/dist/buefy.css'
import '@mdi/font/css/materialdesignicons.css'
import "vue-wysiwyg/dist/vueWysiwyg.css"

//axios.defaults.baseURL = 'https://api.presenza.neuro3.com';
axios.defaults.baseURL = 'http://localhost'

/** https://stackoverflow.com/a/6117889 */
Date.prototype.getWeekNumber = function(){
  var d = new Date(Date.UTC(this.getFullYear(), this.getMonth(), this.getDate()))
  var dayNum = d.getUTCDay() || 7
  d.setUTCDate(d.getUTCDate() + 4 - dayNum)
  var yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1))
  return Math.ceil((((d - yearStart) / 86400000) + 1)/7)
}

Vue.use(VueFilterDateFormat, {
	dayOfWeekNames: ['Domenica', 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato'],
	dayOfWeekNamesShort: ['Do', 'Lu', 'Ma', 'Me', 'Gi', 'Ve', 'Sa'],
	monthNames: ['Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'],
	monthNamesShort: ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic'],
	timezone: 2
})

Vue.use(Vuex)
Vue.use(Buefy)
Vue.use(wysiwyg, {forcePlainTextOnPaste: true, hideModules: {"code": true, "headings": true, "image": true, "table": true, "link": true, "removeFormat": true}, locale: 'it', maxHeight: "300px"})
Vue.config.productionTip = false
Vue.prototype.$http = axios
Vue.prototype.$lodash = lodash

const store = new Vuex.Store({
  state: {
    user: {
      userName: '',
      loggedInStatus: false,
      authToken: ''
    }
  },
  mutations: {
    addWebToken: function(state, webtoken) {
      state.user.authToken = webtoken;
    },
    removeWebToken: function(state) {
      state.user.authToken = '';
    }
  },
  actions: {
    login: function(context, userInput) {
      console.log(userInput)
      axios.post(`/auth/login`, userInput).then(({data}) => {
        context.commit('addWebToken', data.webtoken);
        router.push({name: 'Home'});
      })
    },
    logout: function(context) {
      context.commit('removeWebToken');
      router.push({name: 'GoodBye'});
    }
  }
})
new Vue({
  router,
  store: store,
  render: h => h(App)
}).$mount('#app')
