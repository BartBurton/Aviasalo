<template>
  <div v-show="tkts.length" id="tktlist">
    <v-row v-show="tkts.length > 1">
      <v-col cols="3">
        <v-select
          v-model="manipulators.companiesFilter"
          :items="tkts.map((e) => e.aircompany.name)"
          color="#ce6f61"
          dark
          :menu-props="{
            top: true,
            offsetY: true,
            transition: 'scale-transition',
            maxHeight: 'auto',
          }"
          chips
          multiple
          label="Авиакомпания"
          item-color="#00131a"
          return-object
        >
        </v-select>
      </v-col>
      <v-col offset="1" cols="4" align-self="end">
        <v-slider
          v-model="manipulators.priceFilter"
          :max="manipulators.maxPrice"
          :min="manipulators.minPrice"
          color="#ce6f61"
          label="Цена"
          thumb-color="#ce6f61"
          thumb-label="always"
          dark
          class="align-center"
        >
        </v-slider>
      </v-col>
      <v-col offset="1" cols="3">
        <v-select
          v-model="manipulators.sort"
          item-text="text"
          item-value="value"
          :items="manipulators.sorts"
          color="#ce6f61"
          dark
          :menu-props="{
            top: true,
            offsetY: true,
            transition: 'scale-transition',
            maxHeight: 'auto',
          }"
          label="Сортировать"
          item-color="#00131a"
          return-object
        >
        </v-select>
      </v-col>
    </v-row>

    <v-row class="transition-row">
      <v-col cols="12">
        <transition-group name="tkt-list">
          <ticket
            v-for="(tkt, i) in mutatedTkts"
            :key="`tkt${i}`"
            :tkt="tkt"
            @buy="bookTkt"
            class="tkt-list-item"
          ></ticket>
        </transition-group>
      </v-col>
    </v-row>
    <buy-form :show="buy" :tkt="buyTkt" @close="buy = false"></buy-form>
  </div>
</template>

<script>
import Ticket from './Ticket'
import DateProcessor from '../mixins/DateProccessor'
import BuyForm from './BuyForm'

export default {
  name: 'Tktlist',
  mixins: [DateProcessor],
  components: {
    Ticket, BuyForm
  },
  props: { tkts: Array },

  data: () => ({
    buy: false,
    buyTkt: null,

    mutatedTkts: [],

    manipulators: {
      sort: { text: '', value: null },
      sorts: [],

      companies: [],
      companiesFilter: [],

      minPrice: 0,
      maxPrice: 0,
      priceFilter: 0,
    },
    manipulatorsDelay: null,
  }),

  watch: {
    manipulators: {
      deep: true,
      handler() {
        clearTimeout(this.manipulatorsDelay)

        this.manipulatorsDelay = setTimeout(() => {
          this.mutatedTkts = this.manipulators.sort.value.func()
            .filter(e => e.ticket.price <= this.manipulators.priceFilter)
            .filter(e => this.manipulators.companiesFilter.includes(e.aircompany.name))

          this.takeFocus()
        }, 300)

      }
    }
  },

  mounted() {
    if (!this.tkts.length) {
      this.$destroy()
      return
    }

    this.initSorts()
    let sortedTkts = this.manipulators.sort.value.func()

    this.manipulators.minPrice = sortedTkts[0].ticket.price
    this.manipulators.priceFilter =
      this.manipulators.maxPrice = sortedTkts[sortedTkts.length - 1].ticket.price

    this.manipulators.companies = [... new Set(this.tkts.map(e => e.aircompany.name))]
    this.manipulators.companiesFilter = [...this.manipulators.companies]

    this.takeFocus()
  },

  methods: {
    //Sorts
    initSorts() {
      this.manipulators.sorts = [
        { text: 'Цена по возрастанию', value: { index: 0, func: () => this.orderByPrice() } },
        { text: 'Цена по убыванию', value: { index: 1, func: () => this.orderByPrice(true) } },

        { text: 'Время полета по возрастанию', value: { index: 2, func: () => this.orderByTime() } },
        { text: 'Время полета по убыванию', value: { index: 3, func: () => this.orderByTime(true) } },

        { text: 'Пересадки по возрастанию', value: { index: 4, func: () => this.orderByStops() } },
        { text: 'Пересадки по убыванию', value: { index: 5, func: () => this.orderByStops(true) } },

        { text: 'Места по возрастанию', value: { index: 6, func: () => this.orderBySeats() } },
        { text: 'Места по убыванию', value: { index: 7, func: () => this.orderBySeats(true) } },
      ]
      this.manipulators.sort = this.manipulators.sorts[0]
    },
    orderByPrice(descending = false) {
      return this.tkts.sort((a, b) => {
        let c1 = Number(a.ticket.price)
        let c2 = Number(b.ticket.price)
        return descending ? c2 - c1 : c1 - c2
      })
    },
    orderByTime(descending = false) {
      return this.tkts.sort((a, b) => {
        let s1 = a.segments
        let s2 = b.segments

        let c1 = this.substractDates(new Date(s1[s1.length - 1].arrival_date), new Date(s1[0].departure_date))
        let c2 = this.substractDates(new Date(s2[s2.length - 1].arrival_date), new Date(s2[0].departure_date))

        return descending ? c2 - c1 : c1 - c2
      })
    },
    orderByStops(descending = false) {
      return this.tkts.sort((a, b) => {
        let c1 = Number(a.segments.length)
        let c2 = Number(b.segments.length)
        return descending ? c2 - c1 : c1 - c2
      })
    },
    orderBySeats(descending = false) {
      return this.tkts.sort((a, b) => {
        let c1 = Number(a.ticket.count)
        let c2 = Number(b.ticket.count)
        return descending ? c2 - c1 : c1 - c2
      })
    },

    bookTkt(tkt){
      this.buy = true
      this.buyTkt = tkt
    },

    //Support
    takeFocus() {
      this.$vuetify.goTo('#tktlist', {
        offset: 100,
      })
    }
  },
}
</script>

<style>
.theme--light.v-sheet {
  background-color: #00131a !important;
}
.v-list-item {
  background-color: #00131a;
  color: white !important;
}
.v-list-item--active {
  color: #ce6f61 !important;
}

.tkt-list-item {
  transition: all 1s;
}
.tkt-list-enter,
.tkt-list-leave-to {
  opacity: 0;
  transform: translateY(30px);
}

.tkt-list-leave-active {
  opacity: 0;
  transform: translateX(30px);
}
</style>



