import store from "../store"
import axios from 'axios'

const USER = { type: 'user', signin: '/signin' }
const ADMIN = { type: 'admin', signin: '/signin-maker' }
const MAKER = { type: 'maker', signin: '/signin-maker' }

export default {
    user: USER,
    admin: ADMIN,
    maker: MAKER,



    nextViaCheckAuth: async to => {
        let token = localStorage.Authorization
        let next = ''
        let type = null

        if (to.meta == USER) {
            next = USER.signin
            type = 'IsUser'
        } else if (to.meta == MAKER) {
            next = MAKER.signin
            type = 'IsMaker'
        } else if (to.meta == ADMIN) {
            next = ADMIN.signin
            type = 'IsAdmin'
        } else {
            return to.path
        }

        let query = `/Authenticate/${type}`
        try {
            let resp = await axios.post(query, { token: token })
            if (resp.data) {
                return to.path
            }
        } catch { }
        return next
    },


    setStore: async () => {
        let token = localStorage.Authorization
        let type = localStorage.type
        let user = null;

        if (type && token) {
            axios.defaults.headers.common['Authorization'] = token

            try {
                if ((await axios.post(`/Authenticate/${type}`, { token: token })).data) {
                    if (type == 'IsUser') {
                        user = (await axios.get('/Passanger/Get')).data.passanger
                    } else if (type == 'IsMaker') {
                        user = (await axios.get('/Admin/Get')).data
                    }

                    if (user) {
                        store.commit('setUser', user)
                    }
                }
            } catch { }
        }
    },

    signUpUser: async user => {
        try {
            let query = `/Passanger/Create/`
            let newUser = (await axios.post(query, user)).data
            let token = newUser.remember_token

            if (token) {
                axios.defaults.headers.common['Authorization'] = token
                localStorage.type = 'IsUser'
                localStorage.Authorization = token

                store.commit('setUser', newUser)

                return true
            }
        } catch { }

        return false
    },

    signInUser: async user => {
        try {
            let query = `/Passanger/SignIn/${user.email}/${user.password}`
            let token = (await axios.get(query)).data

            if (token) {
                axios.defaults.headers.common['Authorization'] = token
                localStorage.type = 'IsUser'
                localStorage.Authorization = token

                let userData = (await axios.get('/Passanger/Get')).data.passanger
                store.commit('setUser', userData)

                return true
            }
        } catch { }

        return false
    },

    signInMaker: async maker => {
        try {
            let query = `/Maker/SignIn/${maker.name}/${maker.password}`
            let token = (await axios.get(query)).data

            if (token) {
                axios.defaults.headers.common['Authorization'] = token
                localStorage.type = 'IsMaker'
                localStorage.Authorization = token

                let makerData = (await axios.get('/Admin/Get')).data
                store.commit('setUser', makerData)

                return true
            }
        } catch { }

        return false
    },

    signOut: () => {
        localStorage.removeItem('Authorization')
        localStorage.removeItem('type')
        delete axios.defaults.headers.common['Authorization']
        store.commit('setUser', null)
    }
}