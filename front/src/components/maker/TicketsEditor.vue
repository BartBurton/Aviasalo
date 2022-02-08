<template>
  <v-form ref="tktForm">
    <input
      @change="tktFileUpload()"
      type="file"
      id="tktFile"
      ref="tktFile"
      style="display: none"
      accept="image/png, image/jpeg"
    />
    <v-dialog v-model="tktCommonDateTimePicker" persistent width="50%">
      <v-card>
        <v-card-text>
          <v-row class="pt-10">
            <v-col cols="6" class="text-center">
              <v-date-picker
                v-model="updatedDate"
                locale="ru"
                color="#ce6f61"
                :min="new Date().toISOString().substr(0, 10)"
              >
                <v-spacer></v-spacer>
                <div style="height: 0px"></div>
              </v-date-picker>
            </v-col>
            <v-col cols="6" class="text-center">
              <v-time-picker
                v-model="updatedTime"
                color="#ce6f61"
                format="24hr"
              ></v-time-picker>
            </v-col>
          </v-row>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions class="pr-10 pb-10">
          <v-spacer></v-spacer>
          <v-btn
            color="#ce6f61"
            @click="
              tktCommonDateTimePicker = false;
              changeDate();
            "
          >
            Готово
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-container class="pa-8">
      <v-row>
        <v-col class="text-right">
          <v-btn color="teal accent-3" small fab @click="addTkt()"
            ><v-icon>mdi-plus</v-icon></v-btn
          >
        </v-col>
      </v-row>
      <v-row v-for="(tkt, i) in tickets" :key="`tkt-${i}`">
        <v-col>
          <v-card :class="i % 2 == 0 ? 'item0' : 'item1'" class="pa-2">
            <v-card-title>
              <v-hover v-slot="{ hover }">
                <v-img
                  class="rounded-lg file-uploader"
                  @click="loadTktFileHandler(tkt)"
                  :src="`${$store.getters.storage}/${tkt.ticket.preview_img}`"
                  :lazy-src="`${$store.getters.storage}/previews/lazy.jpg`"
                  @error="errorLoadTktImg(tkt)"
                  :gradient="
                    hover
                      ? '0deg, rgba(0,19,26,1) 0%, rgba(0,19,26,0.9) 60%, rgba(0,19,26,0.8) 100%'
                      : '0deg, rgba(0,19,26,1) 0%, rgba(0,19,26,0.5511122417717087) 60%, rgba(0,19,26,0.3046136423319328) 100%'
                  "
                >
                  <template v-slot:placeholder>
                    <v-row
                      class="fill-height ma-0"
                      align="center"
                      justify="center"
                    >
                      <v-progress-circular
                        indeterminate
                        color="grey lighten-5"
                      ></v-progress-circular>
                    </v-row> </template
                ></v-img>
              </v-hover>
            </v-card-title>

            <v-card-text>
              <v-row v-if="tkt.segments.length == 0">
                <v-col class="pa-5">
                  <v-btn
                    color="teal accent-3"
                    outlined
                    small
                    fab
                    @click="addSegment({ arrival: '', order: -1 }, tkt)"
                    ><v-icon>mdi-plus</v-icon></v-btn
                  >
                </v-col>
              </v-row>
              <v-row v-else style="position: relative">
                <v-col
                  v-for="(seg, j) in tkt.segments"
                  :key="`seg-${j}`"
                  cols="6"
                >
                  <v-card>
                    <v-card-title class="pt-2">
                      <v-btn
                        v-if="j != 0"
                        color="light-green accent-2"
                        outlined
                        x-small
                        fab
                        class="ma-2"
                        @click="movePrev(seg, tkt)"
                        ><v-icon>mdi-chevron-left </v-icon></v-btn
                      >
                      <v-spacer></v-spacer>
                      <v-btn
                        v-if="j != tkt.segments.length - 1"
                        color="light-green accent-2"
                        outlined
                        x-small
                        fab
                        class="ma-2"
                        @click="moveNext(seg, tkt)"
                        ><v-icon>mdi-chevron-right </v-icon></v-btn
                      >
                    </v-card-title>
                    <v-card-text>
                      <v-row>
                        <v-col cols="6">
                          <v-text-field
                            v-model="seg.departure"
                            :rules="requireRules"
                            color="#ce6f61"
                            label="Откуда"
                            dark
                          ></v-text-field>
                        </v-col>
                        <v-col cols="6">
                          <v-text-field
                            v-model="seg.arrival"
                            :rules="requireRules"
                            color="#ce6f61"
                            label="Куда"
                            dark
                          ></v-text-field>
                        </v-col>
                      </v-row>
                      <v-row class="py-0">
                        <v-col cols="6" class="text-center py-0">
                          <v-chip
                            link
                            color="#001118"
                            @click="openDialogToChangeDate(seg, 'departure')"
                          >
                            <span class="body-2 indigo--text text--lighten-4"
                              >{{ seg.departure_date | flyDate }}&nbsp;
                            </span>
                            <span class="body-1 text-color-important">{{
                              seg.departure_date | flyTime
                            }}</span></v-chip
                          >
                        </v-col>
                        <v-col cols="6" class="text-center py-0">
                          <v-chip
                            link
                            color="#001118"
                            @click="openDialogToChangeDate(seg, 'arrival')"
                          >
                            <span class="body-2 indigo--text text--lighten-4"
                              >{{ seg.arrival_date | flyDate }}&nbsp;
                            </span>
                            <span class="body-1 text-color-important">{{
                              seg.arrival_date | flyTime
                            }}</span></v-chip
                          >
                        </v-col>
                      </v-row>
                    </v-card-text>
                    <v-card-actions>
                      <v-btn
                        v-if="tkt.segments.length > 1"
                        color="red darken-1"
                        class="ma-2"
                        outlined
                        x-small
                        fab
                        @click="deleteSegment(seg, tkt)"
                        ><v-icon>mdi-delete-outline</v-icon></v-btn
                      >
                      <v-spacer></v-spacer>
                      <v-btn
                        color="teal accent-3"
                        x-small
                        fab
                        class="ma-2"
                        @click="saveSegment(seg, tkt)"
                        ><v-icon>mdi-content-save </v-icon></v-btn
                      >
                      <v-btn
                        v-if="j == tkt.segments.length - 1"
                        color="amber darken-2"
                        x-small
                        outlined
                        fab
                        class="ma-2"
                        @click="addSegment(seg, tkt)"
                        ><v-icon>mdi-plus</v-icon></v-btn
                      >
                    </v-card-actions>
                  </v-card>
                </v-col>
              </v-row>
            </v-card-text>

            <v-card-actions>
              <v-row align="center" class="px-5">
                <v-col cols="3">
                  <v-text-field
                    v-model="tkt.ticket.price"
                    :rules="requireRules"
                    color="#ce6f61"
                    type="number"
                    label="Цена"
                    dark
                  ></v-text-field>
                </v-col>
                <v-col cols="2">
                  <v-text-field
                    v-model="tkt.ticket.count"
                    :rules="requireRules"
                    type="number"
                    color="#ce6f61"
                    label="Количество мест"
                    dark
                  ></v-text-field>
                </v-col>
                <v-col cols="4">
                  <v-select
                    v-model="tkt.aircompany.id"
                    item-text="name"
                    item-value="id"
                    :items="aircompanies"
                    color="#ce6f61"
                    dark
                    :menu-props="{
                      bottom: true,
                      offsetY: true,
                      transition: 'scale-transition',
                      maxHeight: 'auto',
                    }"
                    label="Авиакомпания"
                    item-color="#00131a"
                  >
                  </v-select>
                </v-col>
                <v-col class="text-right">
                  <v-btn
                    v-if="isAdmin"
                    color="red darken-1"
                    class="ma-2"
                    outlined
                    x-small
                    fab
                    @click="deleteTkt(tkt)"
                    ><v-icon>mdi-delete-outline</v-icon></v-btn
                  >
                  <v-btn
                    color="teal accent-3"
                    class="ma-2"
                    x-small
                    fab
                    @click="saveTkt(tkt)"
                    ><v-icon>mdi-content-save </v-icon></v-btn
                  >
                </v-col>
              </v-row>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>
    </v-container>

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
  </v-form>
</template>

<script>
import moment from 'moment'
export default {
  name: 'TicketsEditor',
  props: {
    isAdmin: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      tickets: null,
      aircompanies: null,

      requireRules: [(v) => !!v || 'Поле обязательно!'],
      tktUpdateFile: null,

      tktCommonDateTimePicker: false,
      point: '',
      updatedDate: null,
      updatedTime: null,
      segmentUpdateDate: null,

      load: false,
      error: { show: false, message: 'Не удалось выполнить оперцию!' },
      success: { show: false, message: 'Операция успешно выполнена!' },
    }
  },

  async mounted() {
    this.load = true

    try {

      let query = `/Aircompany/All`
      let resp = await this.$axios.get(query)
      if (resp.data) { this.aircompanies = resp.data }

      query = `/Ticket/All`
      resp = await this.$axios.get(query)
      if (resp.data) { this.tickets = resp.data }

    } catch { }


    this.load = false
  },



  methods: {
    async addTkt() {
      this.load = true

      let query = `/Ticket/Create/`
      let tkt = {
        aircompany: this.aircompanies[0],
        segments: [],
        ticket: {
          id: 0,
          count: 1,
          price: 1,
          preview_img: ''
        }
      }

      let resp
      try {
        resp = await this.$axios.post(query, {
          aircompany_id: this.aircompanies[0].id,
          count: 1,
          price: 1,
          preview_img: 'previews'
        })


        if (resp.data) {
          tkt.segments.push({
            departure: '',
            arrival: '',
            departure_date: (new Date()).toISOString(),
            arrival_date: (new Date()).toISOString(),
            order: 0,
            ticket_id: resp.data.id,
            new: true
          })
          tkt.ticket.id = resp.data.id
          this.tickets.unshift(tkt)

          this.success.show = true
        } else {
          this.error.show = true
        }

      } catch { this.error.show = true }
      finally { this.load = false }
    },
    loadTktFileHandler(tkt) {
      this.tktUpdateFile = tkt
      this.$refs.tktFile.click()
    },
    async tktFileUpload() {
      let formData = new FormData()
      formData.append('file', this.$refs.tktFile.files[0])

      if (this.$refs.tktFile.files[0]) {
        this.load = true

        let config = {
          headers: {
            'content-type': 'multipart/form-data'
          }
        }

        let resp
        try {
          resp = await this.$axios.post(`/Ticket/LoadPreview/${this.tktUpdateFile.ticket.id}`, formData, config)
          if (resp.data) {
            this.tktUpdateFile.ticket.preview_img = resp.data
            this.success.show = true
          } else {
            this.error.show = true
          }
        } catch { this.error.show = true }
        finally { this.load = false }
      }
    },
    errorLoadTktImg(tkt) {
      tkt.ticket.preview_img = `previews/lazy.jpg`
    },
    async saveTkt(tkt) {
      if (this.$refs.tktForm.validate()) {
        this.load = true

        let query = `/Ticket/Update/${tkt.ticket.id}`
        let data = {
          price: tkt.ticket.price,
          count: tkt.ticket.count,
          aircompany_id: tkt.aircompany.id
        }

        let resp
        try {
          resp = await this.$axios.post(query, data)

          if (resp.data) {
            this.success.show = true
          } else {
            this.error.show = true
          }

        } catch { this.error.show = true }
        finally { this.load = false }
      }
    },
    async deleteTkt(tkt) {
      if (this.$store.getters.user.role == 'admin') {
        this.load = true

        let query = `/Ticket/Delete/${tkt.ticket.id}`

        let resp
        try {
          resp = await this.$axios.post(query)

          if (resp.data) {
            this.tickets.splice(this.tickets.indexOf(tkt), 1)
            this.success.show = true
          } else {
            this.error.show = true
          }
        } catch { this.error.show = true }
        finally { this.load = false }
      }
    },

    openDialogToChangeDate(seg, point) {
      this.point = point
      let date = point == 'departure' ? seg.departure_date : seg.arrival_date
      this.updatedDate = date.substr(0, 10)
      this.updatedTime = moment(date).format('HH:mm')
      this.segmentUpdateDate = seg
      this.tktCommonDateTimePicker = true
    },
    changeDate() {
      let date = `${this.updatedDate}T${this.updatedTime}`
      if (this.point == 'departure') {
        this.segmentUpdateDate.departure_date = date
      } else {
        this.segmentUpdateDate.arrival_date = date
      }
    },
    async deleteSegment(seg, tkt) {
      if (seg.new == undefined) {
        this.load = true

        let query = `/Segment/Delete/${seg.id}`

        let resp
        try {
          resp = await this.$axios.post(query)

          if (resp.data) {
            tkt.segments.splice(tkt.segments.indexOf(seg),)
            this.success.show = true
          } else {
            this.error.show = true
          }
        } catch { this.error.show = true }
        finally { this.load = false }
      } else {
        tkt.segments.splice(tkt.segments.indexOf(seg),)
      }
    },
    async saveSegment(seg, tkt) {
      if (this.$refs.tktForm.validate()) {
        this.load = true

        let query
        if (seg.new == undefined) {
          query = `/Segment/Update/${seg.id}`
        } else {
          query = `/Segment/Create`
        }
        let data = {
          departure: seg.departure,
          arrival: seg.arrival,
          departure_date: seg.departure_date,
          arrival_date: seg.arrival_date,
          order: seg.order,
          ticket_id: tkt.ticket.id
        }

        let resp
        try {
          resp = await this.$axios.post(query, data)

          if (resp.data) {
            this.success.show = true
            delete seg.new
          } else {
            this.error.show = true
          }

        } catch { this.error.show = true }
        finally { this.load = false }
      }
    },
    async moveNext(seg, tkt) {
      if (this.$refs.tktForm.validate()) {
        let next = tkt.segments[tkt.segments.indexOf(seg) + 1]

        let pos = seg.order
        seg.order = next.order
        next.order = pos

        tkt.segments = tkt.segments.sort((a, b) => { return a.order - b.order })

        await this.saveSegment(seg, tkt)
        await this.saveSegment(next, tkt)
      }
    },
    async movePrev(seg, tkt) {
      let prev = tkt.segments[tkt.segments.indexOf(seg) - 1]

      let pos = seg.order
      seg.order = prev.order
      prev.order = pos

      tkt.segments = tkt.segments.sort((a, b) => { return a.order - b.order })

      await this.saveSegment(seg, tkt)
      await this.saveSegment(prev, tkt)
    },
    addSegment(seg, tkt) {
      let data = {
        departure: seg.arrival,
        arrival: '',
        departure_date: new Date().toISOString(),
        arrival_date: new Date().toISOString(),
        order: seg.order + 1,
        ticket_id: tkt.ticket.id,
        new: true
      }
      tkt.segments.push(data)
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
.item0 {
  background-color: #2b102b !important;
}
.item1 {
  background-color: #0c0d27 !important;
}
.file-uploader {
  cursor: pointer;
}
</style>