import store from "../store"
import axios from 'axios'

const USER = { type: 'auth', query: '/Authenticate/IsUser', signin: '/signin' }
const ADMIN = { type: 'auth', query: '/Authenticate/IsAdmin', signin: '/signin-admin' }
const MAKER = { type: 'auth', query: '/Authenticate/IsMaker', signin: '/signin-maker' }


async function signIn(query, getter) {
    try {
        let token = (await axios.get(query)).data

        if (token) {
            axios.defaults.headers.common['Authorization'] = token
            localStorage.Authorization = token
            localStorage.Get = getter

            let user = (await axios.get(getter)).data
            store.commit('setUser', user)

            return true
        }
    } catch { }

    return false
}


export default {
    user: USER,
    admin: ADMIN,
    maker: MAKER,

    isAuth: async to => {
        if (to.meta.type === 'auth') {
            let token = localStorage.Authorization
            if (!!token) {
                try {
                    return !!(await axios.post(to.meta.query, { token: token })).data
                } catch {
                    return false
                }
            }
            return false
        }
        return true
    },


    trySignIn: async () => {
        let token = localStorage.Authorization
        let getter = localStorage.Get

        if (getter && token) {
            axios.defaults.headers.common['Authorization'] = token
            try {
                let user = (await axios.get(getter)).data
                store.commit('setUser', user)
            } catch { }
        }
    },

    signUpUser: async user => {
        try {
            await axios.post(`/Passanger/Create/`, user)
            return await signIn(`/Passanger/SignIn/${user.email}/${user.password}`, '/Passanger/Get')
        } catch { }

        return false
    },

    signInUser: async user =>
        await signIn(`/Passanger/SignIn/${user.email}/${user.password}`, '/Passanger/Get'),

    signInMaker: async maker =>
        await signIn(`/Maker/SignIn/${maker.name}/${maker.password}`, '/Admin/Get'),

    signInAdmin: async admin =>
        await signIn(`/Admin/SignIn/${admin.name}/${admin.password}`, '/Admin/Get'),

    signOut: () => {
        localStorage.removeItem('Authorization')
        localStorage.removeItem('type')
        delete axios.defaults.headers.common['Authorization']
        store.commit('setUser', null)
    }
}