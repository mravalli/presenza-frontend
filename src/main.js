import Vue from 'vue'
import VueFilterDateFormat from '@vuejs-community/vue-filter-date-format';
import axios from 'axios';
import lodash from 'lodash';
import App from './App.vue'
import router from './router'
import Buefy from 'buefy'
import wysiwyg from "vue-wysiwyg";
import 'buefy/dist/buefy.css'
import '@mdi/font/css/materialdesignicons.css'
import "vue-wysiwyg/dist/vueWysiwyg.css";

axios.defaults.baseURL = 'http://localhost';

Vue.use(VueFilterDateFormat, {
	dayOfWeekNames: ['Domenica', 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato'],
	dayOfWeekNamesShort: ['Do', 'Lu', 'Ma', 'Me', 'Gi', 'Ve', 'Sa'],
	monthNames: ['Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'],
	monthNamesShort: ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic'],
	timezone: 2
});
Vue.use(Buefy);
Vue.use(wysiwyg, {forcePlainTextOnPaste: true, hideModules: {"code": true, "headings": true, "image": true, "table": true, "link": true, "removeFormat": true}, locale: 'it', maxHeight: "300px"});
Vue.config.productionTip = false;
Vue.prototype.$http = axios;
Vue.prototype.$lodash = lodash;

new Vue({
  router,
  render: h => h(App)
}).$mount('#app')
