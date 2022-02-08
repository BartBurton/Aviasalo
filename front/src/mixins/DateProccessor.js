import moment from 'moment'

export default {
    methods:{
        substractDates(date1, date2){
            let d1 = new Date(date1)
            let d2 = new Date(date2)
            return Math.abs(d1 - d2)
        },
        
        msToCountDHM(ms){
            let seconds = Number(ms) / 60000

            let days = parseInt(seconds / (60 * 24))
            let hours = parseInt(seconds / 60)
            let minutes = seconds % 60
      
            return (!!days ? `${days}д ` : '') + moment(new Date(0, 0, 0, hours, minutes)).format('Hч mм')
        },
    }
}