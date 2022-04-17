import Vue from 'vue'
import VueRouter from 'vue-router'

import Home from '../views/Home.vue'

import PageMaker from '../views/PageMaker.vue'
import PageAdmin from '../views/PageAdmin.vue'
import PageUser from '../views/PageUser.vue'

import SignInMaker from '../views/Login/SignInMaker.vue'
import SignInAdmin from '../views/Login/SignInAdmin.vue'
import SignInUser from '../views/Login/SignInUser.vue'
import SignUpUser from '../views/Login/SignUpUser.vue'

import NotFound from '../views/NotFound.vue'

import authenticator from '../plugins/authanticator'

Vue.use(VueRouter)

const routes = [
  {
    path: '*',
    component: NotFound,
  },

  {
    path: '/home',
    name: 'Home',
    component: Home,
  },
  {
    path: '/',
    redirect: '/home',
  },


  {
    path: authenticator.user.signin,
    name: 'SignInUser',
    props: router => ({
      next: router.query.next
    }),
    component: SignInUser,
  },
  {
    path: '/signup',
    name: 'SignUpUser',
    props: router => ({
      next: router.query.next
    }),
    component: SignUpUser,
  },
  {
    path: authenticator.maker.signin,
    name: 'SignInMaker',
    props: router => ({
      next: router.query.next
    }),
    component: SignInMaker,
  },
  {
    path: authenticator.admin.signin,
    name: 'SignInAdmin',
    props: router => ({
      next: router.query.next
    }),
    component: SignInAdmin,
  },


  {
    path: '/profile',
    name: 'PageUser',
    component: PageUser,
    meta: authenticator.user
  },

  {
    path: '/maker',
    redirect: '/maker/editor',
    name: 'PageMaker',
    component: PageMaker,
    children: [
      {
        path: 'editor',
        redirect: '/maker/editor/tickets',
        name: 'Editor',
        component: () => import('../components/maker/Editor.vue'),
        children: [
          {
            path: 'tickets',
            name: 'TicketsEditor',
            component: () => import('../components/maker/TicketsEditor.vue'),
            meta: authenticator.maker
          },
          {
            path: 'aircompanies',
            name: 'AircompaniesEditor',
            component: () => import('../components/maker/AircompaniesEditor.vue'),
            meta: authenticator.maker
          },
        ],
        meta: authenticator.maker
      },
    ],
    meta: authenticator.maker
  },

  {
    path: '/admin',
    redirect: '/admin/editor',
    name: 'PageAdmin',
    component: PageAdmin,
    children: [
      {
        path: 'editor',
        redirect: { name: 'TicketsEditor' },
        name: 'Editor',
        component: () => import('../components/maker/Editor.vue'),
        children: [
          {
            path: 'makers',
            name: 'MakersEditor',
            component: () => import('../components/maker/MakersEditor.vue'),
            meta: authenticator.admin
          },
          {
            path: 'passangers',
            name: 'PassangersOserver',
            component: () => import('../components/maker/PassangersOserver.vue'),
            meta: authenticator.admin
          },
        ],
        meta: authenticator.admin
      },
    ],
    meta: authenticator.admin
  },
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

router.beforeEach(async (to, from, next) => {
  window.scroll({ top: 0, behavior: 'smooth' })

  if (await authenticator.isAuth(to)) {
    next()
  } else {
    next(`${to.meta.signin}?next=${encodeURIComponent(to.path)}`)
  }
})

export default router
