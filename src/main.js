import Vue from 'vue'

import Buefy from 'buefy'
import lodash from 'lodash'
import VueFilterDateFormat from '@vuejs-community/vue-filter-date-format'
import wysiwyg from "vue-wysiwyg"

import axios from './helper/axios'
import store from './store'
import router from './router'
import App from './App.vue'

import 'buefy/dist/buefy.css'
import '@mdi/font/css/materialdesignicons.css'
import "vue-wysiwyg/dist/vueWysiwyg.css"

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

Vue.use(Buefy)
Vue.use(wysiwyg, {forcePlainTextOnPaste: true, hideModules: {"code": true, "headings": true, "image": true, "table": true, "link": true, "removeFormat": true}, locale: 'it', maxHeight: "300px"})
Vue.config.productionTip = false
Vue.prototype.$http = axios
Vue.prototype.$lodash = lodash

new Vue({
  router,
  store,
  beforeCreate() {
    this.$store.commit('initialiseStore')
  },
  render: h => h(App)
}).$mount('#app')
