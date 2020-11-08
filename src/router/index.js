import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../views/Home.vue'

Vue.use(VueRouter)

const routes = [
  { path: '/', name: 'Home', component: Home },
  { path: '/about', name: 'About', component: () => import('../views/About.vue') },

  { path: '/impostazioni', name: 'Impostazioni', component: () => import ('../views/Settings.vue') },
  
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

export default router
