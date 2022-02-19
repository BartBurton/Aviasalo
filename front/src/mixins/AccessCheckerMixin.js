export default {
    data: () => ({
        check: false,
        allow: () => { },
        deny: () => { },
    }),
    methods: {
        doViaCheckAccess(funcAllowed = () => { }, funcDenied = () => { }) {
            this.check = true

            this.allow = () => {
                funcAllowed()
                this.check = false
            }

            this.deny = () => {
                funcDenied()
                this.check = false
            }
        }
    }
}