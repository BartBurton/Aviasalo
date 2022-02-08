<template>
  <v-row>
    <v-col cols="12">
      <v-card elevation="10" color="#00131a" dark class="tkt-border my-4">
        <v-img v-if="type == 'show'"
          :height="'100%'"
          :src="`${$store.getters.storage}/${tkt.ticket.preview_img}`"
          gradient="0deg, rgba(0,19,26,1) 0%, rgba(0,19,26,0.5511122417717087) 60%, rgba(0,19,26,0.3046136423319328) 100%"
        ></v-img>

        <v-card-title class="px-8" :class="type == 'show' ? 'pt-5' : 'pt-12'">
          <div class="line-container cursor-grab" ref="routeCont">
            <div
              v-for="(seg, i) in tkt.segments"
              :key="`seg${i}`"
              class="line-block"
            >
              <div
                v-if="i != 0"
                class="line-block font-weight-light font-italic"
              >
                <col-info>
                  <template v-slot:top class="">
                    {{ seg.departure }}
                  </template>

                  <v-chip color="#00131a" class="cursor-grab">
                    <v-icon color="yellow accent-3">mdi-sofa-single</v-icon>
                    &nbsp;&nbsp;
                    <span class="yellow--text text--accent-3">{{
                      flySegmentTime({
                        departure_date: seg.departure_date,
                        arrival_date: tkt.segments[i - 1].arrival_date,
                      })
                    }}</span></v-chip
                  >

                  <template v-slot:bottom>
                    <span class="body-2 indigo--text text--lighten-4"
                      >{{ tkt.segments[i - 1].arrival_date | flyDate }}&nbsp;
                    </span>
                    <span class="body-1 text-color-important">{{
                      tkt.segments[i - 1].arrival_date | flyTime
                    }}</span>
                    <v-icon class="mx-2" color="yellow accent-3"
                      >mdi-timer-sand-complete</v-icon
                    >
                    <span class="body-2 indigo--text text--lighten-4"
                      >{{ seg.departure_date | flyDate }}&nbsp;
                    </span>
                    <span class="body-1 text-color-important">{{
                      seg.departure_date | flyTime
                    }}</span>
                  </template>
                </col-info>
              </div>

              <div v-else class="line-block">
                <col-info>
                  <template v-slot:top>
                    {{ seg.departure }}
                  </template>

                  <template v-slot:bottom>
                    <span class="body-2 indigo--text text--lighten-4"
                      >{{ seg.departure_date | flyDate }}&nbsp;
                    </span>
                    <span class="body-1 text-color-important">{{
                      seg.departure_date | flyTime
                    }}</span>
                  </template>
                </col-info>
              </div>

              <div class="line-block text-color-important">
                <col-info
                  :width="i == 0 || i == tkt.segments.length - 1 ? 190 : 230"
                >
                  <template v-slot:top>
                    <v-chip color="#00131a" class="cursor-grab" dark>
                      <v-icon color="teal accent-3"
                        >mdi-airplane-takeoff</v-icon
                      >
                      &nbsp;&nbsp;
                      <span class="teal--text text--accent-3">{{
                        flySegmentTime(seg)
                      }}</span>
                      &nbsp;&nbsp;
                      <v-icon color="teal accent-3"
                        >mdi-airplane-landing</v-icon
                      ></v-chip
                    >
                  </template>
                  - - - - - - - - - - - -
                </col-info>
              </div>
            </div>

            <div class="line-block">
              <col-info>
                <template v-slot:top>
                  {{ tkt.segments[tkt.segments.length - 1].arrival }}
                </template>

                <template v-slot:bottom>
                  <span class="body-2 indigo--text text--lighten-4"
                    >{{
                      tkt.segments[tkt.segments.length - 1].arrival_date
                        | flyDate
                    }}
                    &nbsp;
                  </span>
                  <span class="body-1 text-color-important">{{
                    tkt.segments[tkt.segments.length - 1].arrival_date | flyTime
                  }}</span>
                </template>
              </col-info>
            </div>
          </div>

          <v-spacer></v-spacer>

          <v-chip
            v-if="tkt.segments.length == 1"
            light
            color="teal accent-3"
            class="right-route tkt-tag"
            >Прямой</v-chip
          >
          <v-chip
            v-else
            light
            color="indigo lighten-4"
            class="stops-route tkt-tag"
            >{{ `Пересадок: ${tkt.segments.length - 1}` }}</v-chip
          >
        </v-card-title>

        <v-card-subtitle class="px-12 py-8 subtitle-1">
          <v-row align="center">
            <v-img
              :src="`${$store.getters.storage}/${tkt.aircompany.image_path}`"
              :alt="tkt.aircompany.name"
              class="rounded-lg"
              max-width="128"
            >
            </v-img>

            <h2 v-if="type == 'show'" class="ml-6">{{ tkt.ticket.price }} ₽</h2>

            <v-spacer></v-spacer>

            <v-chip color="" label outlined
              >В пути:&nbsp;<span class="text-color-important">{{
                flyFullTime(tkt)
              }}</span></v-chip
            >
            <span v-if="type != 'show'">&nbsp;&nbsp; Мест:&nbsp;
            <span
              :class="
                tkt.ticket.count > 10
                  ? 'teal--text text--accent-3'
                  : tkt.ticket.count != 1
                  ? 'yellow--text text--accent-3'
                  : 'deep-orange--text text--accent-3'
              "
            >
              {{ tkt.ticket.count }}</span
            ></span>
          </v-row>
        </v-card-subtitle>

        <v-divider></v-divider>
        <v-card-actions v-if="type != 'show'" class="pa-5">
          <h3>{{ tkt.ticket.price }} ₽</h3>
          <v-spacer></v-spacer>
          <v-btn
            color="indigo lighten-4"
            light
            class="px-5"
            outlined
            @click="$emit('buy', tkt)"
          >
            Забронировать
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
import ColInfo from './ColInfo'

import moment from 'moment'
import DateProcessor from '../mixins/DateProccessor'

export default {
  name: 'Ticket',

  mixins: [DateProcessor],

  components: {
    ColInfo
  },

  props: { tkt: Object, type: String },

  data: () => ({
    showBuy: false,
    scrollRoute: false
  }),

  mounted() {
    this.$refs.routeCont.onmousedown = e => {
      this.scrollRoute = true
    }
    this.$refs.routeCont.onmouseup = e => {
      this.scrollRoute = false
    }
    this.$refs.routeCont.onmouseleave = e => {
      this.scrollRoute = false
    }
    this.$refs.routeCont.onmousemove = event => {
      if (this.scrollRoute) {
        let el = this.$refs.routeCont
        el.scrollLeft += -(event.movementX * 0.5)
      }
    }
  },

  methods: {
    flyFullTime(tkt) {
      let date1 = new Date(tkt.segments[0].departure_date)
      let date2 = new Date(tkt.segments[tkt.segments.length - 1].arrival_date)
      return this.msToCountDHM(this.substractDates(date2, date1))
    },
    flySegmentTime(segment) {
      let date1 = new Date(segment.departure_date)
      let date2 = new Date(segment.arrival_date)
      return this.msToCountDHM(this.substractDates(date2, date1))
    }
  },

  filters: {
    flyDate: function (date) {
      return moment(date).format('DD MMM (dd)')
    },
    flyTime: function (date) {
      return moment(date).format('HH:mm')
    }
  },
}
</script>

<style scoped>
.tkt-border {
  border: 1px solid #ce6f61 !important;
}

.line-container {
  max-width: 100%;
  white-space: nowrap;
  overflow-x: auto;
  overflow-y: hidden;
  user-select: none;
}

.cursor-grab {
  cursor: grab;
}

.line-container::-webkit-scrollbar {
  height: 0px;
}

.right-route {
  box-shadow: 0 0 30px #1ce1b086;
}
.stops-route {
  box-shadow: 0 0 30px #bec3e183;
}

.line-block {
  display: inline-block;
}

.tkt-tag {
  position: absolute;
  top: 0;
  right: 24px;
  transform: translateY(-50%);
}
</style>