import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes: [
    {
      path: '/',
      name: 'login',
      component: () => import('@/views/login')
    },
    {
      path: '/home',
      name: 'home',
      component: () => import('@/views/homepage')
    },
    {
      path: '/admin/',
      component: () => import('@/views/admin'),
      children: [
        {
          path: '',
          redirect: { 'name': 'adminSearch' },
        },
        {
          path: 'search',
          name: 'adminSearch',
          component: () => import('@/views/admin_tabs/search')
        },
        {
          path: 'edit_lesson',
          name: 'adminEditLesson',
          component: () => import('@/views/admin_tabs/edit_lesson')
        }
      ]
    }
  ]
})

export default router
