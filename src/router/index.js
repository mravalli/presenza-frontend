import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../views/Home.vue'

Vue.use(VueRouter)

const routes = [
  { path: '/', name: 'Home', component: Home },
  { path: '/login', name: 'Login', component: () => import('../views/Login.vue') },
  { path: '/goodbye', name: 'GoodBye', component: () => import('../views/GoodBye.vue') },
  { path: '/about', name: 'About', component: () => import('../views/About.vue') },

  { path: '/impostazioni', name: 'Impostazioni', component: () => import ('../views/Settings.vue') },
  { path: '/impostazioni/ore', name: 'Ore Settimanali', component: () => import ('../views/HoursWeek/lists.vue') },
  { path: '/impostazioni/ore/nuovo', component: () => import ('../views/HoursWeek/add.vue') },
  { path: '/impostazioni/ore/:id(\\d+)', component: () => import ('../views/HoursWeek/edit.vue') },
  { path: '/impostazioni/giustificativi', name: 'Giustificativi', component: () => import('../views/Justification/lists.vue') },
  { path: '/impostazioni/giustificativo/nuovo', component: () => import('../views/Justification/add.vue') },
  { path: '/impostazioni/giustificativo/:id(\\d+)', component: () => import('../views/Justification/edit.vue') },
  
  { path: '/collaboratori', name: 'Collaboratori', component: () => import('../views/Employees/lists.vue') },
  { path: '/collaboratore/nuovo', component: () => import('../views/Employees/add.vue') },
  { path: '/collaboratore/:id(\\d+)', component: () => import('../views/Employees/profile.vue') },

  { path: '/sedi', name: 'Sedi', component: () => import('../views/Offices/lists.vue') },
  { path: '/sede/nuova', component: () => import('../views/Offices/add.vue') },
  { path: '/sede/:id(\\d+)', component: () => import('../views/Offices/edit.vue') },

  
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

// router.beforeEach((to, from, next) => {
//   if (to.name !== 'Login' ) next ({name: 'Login'})
//   else next()
// })

export default router
