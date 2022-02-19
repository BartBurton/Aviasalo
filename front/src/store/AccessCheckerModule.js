export default {
    state: () => ({
        isChecked: false,
    }),
    mutations: {
        setChecked(state, value) {
            state.isChecked = value
        }
    },
    getters: {
        isChecked(state) { return state.isChecked }
    }
}