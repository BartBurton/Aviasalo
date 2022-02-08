import Vue from 'vue'
import VueRouter from 'vue-router'

import Home from '../views/Home.vue'
import PageMaker from '../views/PageMaker.vue'
import PageAdmin from '../views/PageAdmin.vue'
import PageUser from '../views/PageUser.vue'
import SignInMaker from '../views/SignInMaker.vue'
import SignInUser from '../views/SignInUser.vue'
import SignUpUser from '../views/SignUpUser.vue'
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
    component: SignInUser,
  },
  {
    path: '/signup',
    name: 'SignUpUser',
    component: SignUpUser,
  },
  {
    path: authenticator.maker.signin,
    name: 'SignInMaker',
    component: SignInMaker,
  },


  {
    path: '/profile',
    name: 'PageUser',
    component: PageUser,
    meta: authenticator.user
  },
  {
    path: '/maker',
    name: 'PageMaker',
    component: PageMaker,
    meta: authenticator.maker
  },
  {
    path: '/admin',
    name: 'PageAdmin',
    component: PageAdmin,
    meta: authenticator.admin
  }
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

router.beforeEach(async (to, from, next) => {

  window.scroll({
    top: 0,
    behavior: "smooth"
  });

  let nextPoint = await authenticator.nextViaCheckAuth(to)
  if (nextPoint == to.path) {
    next()
  } else {
    next(nextPoint)
  }
})

export default router
