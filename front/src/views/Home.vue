<template>
  <div>
    <v-row>
      <v-col>
        <search-form @search="getTkts($event)"></search-form>
      </v-col>
    </v-row>

    <v-row v-if="load">
      <v-col class="text-center">
        <div class="lds-ring">
          <div></div>
          <div></div>
          <div></div>
          <div></div>
        </div>
      </v-col>
    </v-row>
    <v-row v-else-if="notkts">
      <v-col class="text-center">
        <h1 class="text text-color-important">Билеты не найдены</h1>
      </v-col>
    </v-row>
    <v-row v-else>
      <v-col class="text-center">
        <h1 class="text text-color-important">
          {{ search.departure }}
          <span v-show="search.departure && search.arrival"> - </span>
          {{ search.arrival }}
        </h1>
        <h3 class="text text-color-default">
          {{ search.date | moment("DD MMM YYYY") }}
        </h3>
      </v-col>
      <v-col cols="12">
        <tktlist :tkts="tkts"></tktlist>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import SearchForm from '../components/SearchForm'
import Tktlist from '../components/Tktlist'

export default {
  name: 'Home',

  components: {
    SearchForm, Tktlist
  },

  data() {
    return {
      tkts: [],

      search: {
        departure: '',
        arrival: '',
        date: null,
      },

      load: false,
      notkts: false,
    }
  },

  methods: {
    async getTkts(search) {
      this.search = search

      if (this.search.departure && this.search.arrival && this.search.date) {
        this.load = true
        this.notkts = true

        let query = `/Ticket/Search/${this.search.departure}/${this.search.arrival}/${this.search.date}`
        let resp = await this.$axios.get(query)

        if (resp.data.length) {
          this.tkts = resp.data
          this.notkts = !this.tkts.filter(e => e.ticket.count > 0).length
        }

        this.load = false
      }
    },
  },

}
</script>

<style scoped>
.lds-ring {
  display: inline-block;
  position: relative;
  width: 80px;
  height: 80px;
}
.lds-ring div {
  box-sizing: border-box;
  display: block;
  position: absolute;
  width: 64px;
  height: 64px;
  margin: 8px;
  border: 8px solid #ce6f61;
  border-radius: 50%;
  animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
  border-color: #ce6f61 transparent transparent transparent;
}
.lds-ring div:nth-child(1) {
  animation-delay: -0.45s;
}
.lds-ring div:nth-child(2) {
  animation-delay: -0.3s;
}
.lds-ring div:nth-child(3) {
  animation-delay: -0.15s;
}
@keyframes lds-ring {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>

