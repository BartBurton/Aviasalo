<template>
  <div>
    <v-dialog
      v-model="show"
      width="50%"
      @click:outside="$emit('close')"
      :closeDelay="0"
    >
      <v-card color="#00131a" dark height="100%">
        <v-card-title v-if="tkt">
          {{ tkt.segments[0].departure }} -
          {{ tkt.segments[tkt.segments.length - 1].arrival }}
          <v-spacer></v-spacer>
          <span class="body-2 indigo--text text--lighten-4"
            >{{ tkt.segments[0].departure_date | flyDate }}&nbsp;
          </span>
          <span class="body-1 text-color-important">{{
            tkt.segments[0].departure_date | flyTime
          }}</span>
        </v-card-title>
        <v-card-title v-else>Забронировать</v-card-title>
        <v-divider></v-divider>

        <v-card-text class="pt-10 px-16">
          <v-form ref="buyForm" v-if="user != null">
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="user.name"
                  :rules="requireRules"
                  color="#ce6f61"
                  label="Имя"
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="user.surname"
                  :rules="requireRules"
                  color="#ce6f61"
                  label="Фамилия"
                  outlined
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="6">
                <v-menu
                  v-model="datepickerMenu"
                  :close-on-content-click="false"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="user.dob"
                      :rules="requireRules"
                      label="Дата рождения"
                      readonly
                      dark
                      outlined
                      color="#ce6f61"
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="user.dob"
                    no-title
                    scrollable
                    locale="ru"
                    color="#ce6f61"
                    :max="new Date().toISOString().substr(0, 10)"
                    :min="'1900-01-01'"
                  >
                    <v-spacer></v-spacer>
                    <v-btn text color="#ce6f61" @click="datepickerMenu = false">
                      ОК
                    </v-btn>
                  </v-date-picker>
                </v-menu>
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  :rules="requireRules"
                  v-model="user.doc"
                  color="#ce6f61"
                  label="Номер документа"
                  outlined
                  dark
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="user.email"
                  :rules="emailRules"
                  color="#ce6f61"
                  label="Email"
                  outlined
                  dark
                ></v-text-field>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>

        <v-divider></v-divider>
        <v-card-actions class="pa-5">
          <v-spacer></v-spacer>
          <v-btn color="#ce6f61" outlined @click="$emit('close')">Отмена</v-btn>
          <v-btn color="#ce6f61" style="color: #00131a" @click="buy">
            <span v-if="!load">Подтвердить</span>
            <span v-else>
              <div class="lds-ring">
                <div></div>
                <div></div>
                <div></div>
                <div></div></div
            ></span>
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

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
  </div>
</template>

<script>
import moment from 'moment'
export default {
  name: 'BuyFrom',
  props: {
    tkt: {
      type: Object,
      default: null
    },
    show: {
      type: Boolean,
      default: true
    },
  },
  data: () => ({

    user: null,


    datepickerMenu: false,
    requireRules: [(v) => !!v || 'Поле обязательно!'],
    emailRules: [
      (v) => !!v || 'Email обязательно!',
      (v) => /.+@.+\..+/.test(v) || 'Email некорректный!'
    ],


    load: false,
    error: { show: false, message: 'Не удалось выполнить операцию!' },
    success: { show: false, message: 'Операция успешно выполнена!' },
  }),

  watch: {
    show: function (newVal, oldVal) {
      if (!this.isEmpty(this.$store.getters.user)) {
        if (!!newVal && this.$store.getters.user.role == undefined) {
          this.user = {
            name: this.$store.getters.user.name,
            surname: this.$store.getters.user.surname,
            dob: this.$store.getters.user.dob,
            doc: this.$store.getters.user.doc,
            email: this.$store.getters.user.email
          }
          return
        }
      }
      this.user = {
        name: '',
        surname: '',
        dob: '',
        doc: '',
        email: ''
      }
    }
  },

  methods: {
    async buy() {
      if (!!this.user) {
        if (!this.$refs.buyForm.validate()) return

        this.load = true
        this.error.show = false
        this.success.show = false

        try {
          let query = `/Booking/Create`
          
          if(this.$store.getters.user !== null && this.$store.getters.user.role === undefined)
          {
            this.user['id'] = this.$store.getters.user.id
          }
          console.log(this.user)
          
          let data = {
            ticket_id: this.tkt.ticket.id,
            passanger: this.user
          }

          let resp = await this.$axios.post(query, data)

          if (resp.data) {
            query = `/Booking/Confirm`
            data = {
              ticket_id: resp.data.ticket_id,
              email: resp.data.email,
              booking_token: resp.data.transaction_token,
            }

            resp = await this.$axios.post(query, data)

            if (resp.data) {
              this.success.show = true
            }
          }

          return
        } catch { }
        finally {
          this.load = false
          this.$emit('close')
        }
      }

      this.error.show = true
      this.load = false
      this.$emit('close')
    },

    isEmpty(obj) {
      return obj == null || Object.keys(obj).length === 0;
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

<style>
.v-dialog::-webkit-scrollbar {
  width: 0px;
}
</style>

<style scoped>
.lds-ring {
  display: inline-block;
  position: relative;
  width: 100px;
  height: 80px;
}
.lds-ring div {
  box-sizing: border-box;
  display: block;
  position: absolute;

  top: 23%;
  left: 27%;

  width: 32px;
  height: 32px;
  margin: 8px;
  border: 4px solid #00131a;
  border-radius: 50%;
  animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
  border-color: #00131a transparent transparent transparent;
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

