import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes: [
    // {
    //   path: '*/*',
    //   name: 'maintenance',
    //   component: () => import('@/views/maintenance')
    // },
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
          path: 'edit_user',
          name: 'adminEditUser',
          component: () => import('@/views/admin_tabs/edit_user')
        },
        {
          path: 'edit_lesson',
          name: 'adminEditLesson',
          component: () => import('@/views/admin_tabs/edit_lesson')
        },
        {
          path: 'edit_lesson/details',
          name: 'adminEditLessonDetails',
          component: () => import('@/views/admin_tabs/edit_lesson_details')
        }
      ]
    }
  ]
})

export default router
