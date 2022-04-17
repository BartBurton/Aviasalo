<template>
  <div>
    <v-row v-if="isMounted">
      <v-col class="text-center pa-16">
        <v-progress-circular
          indeterminate
          color="#ce6f61"
          size="70"
        ></v-progress-circular>
      </v-col>
    </v-row>

    <v-expansion-panels v-else inset dark multiple>
      <v-expansion-panel
        v-for="(tkt, i) in tickets"
        :key="`tkt-${i}`"
        :class="i % 2 == 0 ? 'item0' : 'item1'"
        @click="(e) => initPassangers(e, tkt)"
      >
        <v-expansion-panel-header :id="`tkt${tkt.ticket.id}`">
          <v-row>
            <v-col cols="8">
              <span class="text-body-1 pr-5">
                {{ tkt.segments[0].departure }} -
                {{ tkt.segments[tkt.segments.length - 1].arrival }}</span
              >
              <v-chip color="#00111873" class="pa-4">
                <span class="body-2 indigo--text text--lighten-4"
                  >{{ tkt.segments[0].departure_date | flyDate }}&nbsp;&nbsp;
                </span>
                <span class="body-1 text-color-important">{{
                  tkt.segments[0].departure_date | flyTime
                }}</span
                >&nbsp;&nbsp;&nbsp;&nbsp;

                <span class="body-2 indigo--text text--lighten-4"
                  >{{
                    tkt.segments[tkt.segments.length - 1].arrival_date
                      | flyDate
                  }}&nbsp;&nbsp;
                </span>
                <span class="body-1 text-color-important">{{
                  tkt.segments[tkt.segments.length - 1].arrival_date | flyTime
                }}</span>
              </v-chip>
            </v-col>
            <v-col class="text-right" align-self="center"
              ><span class="pa-4"> {{ tkt.aircompany.name }} </span></v-col
            >
          </v-row>
        </v-expansion-panel-header>
        <v-expansion-panel-content>
          <v-row v-if="tkt.load">
            <v-col class="text-center">
              <div class="lds-ring">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
              </div>
            </v-col>
          </v-row>

          <transition name="fade">
            <v-simple-table
              v-if="tkt.passangers.length && !tkt.load && tkt.shown"
              dark
              max-height="300px"
            >
              <template v-slot:default>
                <thead class="table-back">
                  <tr>
                    <th class="text-left blue-grey--text text--darken-1">
                      Фамилия
                    </th>
                    <th class="text-left blue-grey--text text--darken-1">
                      Имя
                    </th>
                    <th class="text-left blue-grey--text text--darken-1">
                      Дата рождения
                    </th>
                    <th class="text-left blue-grey--text text--darken-1">
                      Документ
                    </th>
                    <th class="text-right blue-grey--text text--darken-1">
                      Почта
                    </th>
                  </tr>
                </thead>

                <tbody class="table-back">
                  <tr
                    v-for="(pass, j) in tkt.passangers"
                    :key="`pass-${j}-tkt-${tkt.ticket.id}`"
                    class="table-row"
                  >
                    <td class="orange--text text--accent-1">
                      {{ pass.surname }}
                    </td>
                    <td class="orange--text text--accent-1">
                      {{ pass.name }}
                    </td>
                    <td>{{ pass.dob | moment("DD.MM.YYYY") }}</td>
                    <td class="deep-orange--text text--accent-1">
                      {{ pass.doc }}
                    </td>
                    <td class="text-right">
                      <v-chip
                        color="light-green lighten-4"
                        label
                        outlined
                        link
                        :href="`mailto:${pass.email}`"
                        >{{ pass.email }}
                        <v-icon right> mdi-at </v-icon></v-chip
                      >
                    </td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
          </transition>

          <transition name="fade">
            <v-row v-if="!tkt.passangers.length && !tkt.load && tkt.shown">
              <v-col class="text-center ma-8">
                <h1 class="font-weight-thin">На рейс нет пассажиров</h1>
              </v-col>
            </v-row>
          </transition>
        </v-expansion-panel-content>
      </v-expansion-panel>
    </v-expansion-panels>

    <access-checker
      v-model="check"
      :duration="30000"
      @allowed="allow"
      @denied="deny"
      @dropSession="
        () => {
          hidePassangers();
        }
      "
    ></access-checker>

    <v-snackbar
      color="#00131a"
      v-model="error.show"
      elevation="10"
      class="pa-5"
    >
      <span :style="`color:${'#ce6f61'};`"> {{ error.message }} </span>
      <template v-slot:action="{ attrs }">
        <v-btn
          color="#ce6f61"
          fab
          outlined
          x-small
          v-bind="attrs"
          @click="error.show = false"
        >
          <v-icon> mdi-close </v-icon>
        </v-btn>
      </template>
    </v-snackbar>

    <v-snackbar
      color="#00131a"
      v-model="success.show"
      elevation="10"
      class="pa-5"
    >
      <span class="teal--text text--accent-3"> {{ success.message }} </span>
      <template v-slot:action="{ attrs }">
        <v-btn
          color="teal accent-3"
          fab
          outlined
          x-small
          v-bind="attrs"
          @click="success.show = false"
        >
          <v-icon> mdi-close </v-icon>
        </v-btn>
      </template>
    </v-snackbar>

    <v-overlay :value="load" z-index="1" color="#00131a" :opacity="0.5">
      <v-progress-circular indeterminate color="#ce6f61" size="70">
      </v-progress-circular>
    </v-overlay>
  </div>
</template>

<script>
import moment from 'moment'
import AccessChecker from './AccessChecker.vue'
import AccessCheckerMixin from '../../mixins/AccessCheckerMixin.js'
export default {
  name: 'PassangersObserver',
  components: { AccessChecker },
  mixins: [AccessCheckerMixin],
  data: () => ({
    tickets: [],

    load: false,
    isMounted: false,
    error: { show: false, message: 'Не удалось выполнить оперцию!' },
    success: { show: false, message: 'Операция успешно выполнена!' },
  }),

  async mounted() {
    this.isMounted = true
    try {
      let resp = await this.$axios.get(`/Ticket/All`)
      if (resp.data) {
        this.tickets = resp.data.map(e => {
          e['passangers'] = []
          e['shown'] = false
          e['load'] = false
          return e
        })
      }
      else { this.error.show = true }

    } catch { this.error.show = true }
    finally { this.isMounted = false }
  },
  methods: {
    initPassangers(e, tkt,) {
      if (!tkt.shown) {
        tkt.load = true
        tkt.shown = true

        this.doViaCheckAccess(async () => {
          tkt.passangers = []

          try {
            let resp = await this.$axios.get(`/Ticket/Passangers/${tkt.ticket.id}`)
            if (resp.data) {
              tkt.passangers = resp.data
              console.log(tkt.passangers)
            }
            else { this.error.show = true }
          } catch { this.error.show = true }
          finally { tkt.load = false }
        }, () => {
          e.target.click()
        })

      } else {
        tkt.shown = false
      }
    },

    hidePassangers() {
      this.tickets.forEach(e => {
        if (e.shown) {
          document.getElementById(`tkt${e.ticket.id}`).click()
        }
      })
    },
  },
  filters: {
    flyDate: function (date) {
      return moment(date).format('DD MMM (dd)')
    },
    flyTime: function (date) {
      return moment(date).format('HH:mm')
    }
  }
}
</script>

<style scoped>
.table-back {
  background-color: #00131a;
}
.table-row:hover {
  background-color: #ffffff0a !important;
}

.item0 {
  background-color: #2b102b !important;
}
.item1 {
  background-color: #0c0d27 !important;
}

.lds-ring {
  display: inline-block;
  position: relative;
  width: 46px;
  height: 46px;
}
.lds-ring div {
  box-sizing: border-box;
  display: block;
  position: absolute;
  width: 46px;
  height: 46px;
  border: 3px solid #ce6f61;
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

.fade-leave-active {
  transition: all 0s;
}
.fade-enter-active {
  transition: all 0.5s;
}
.fade-enter {
  transform: translateY(-20px);
  opacity: 0;
}
</style>