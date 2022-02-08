<template>
  <div>
    <v-form ref="form">
      <v-row>
        <v-col cols="12" sm="4">
          <v-img
            :src="`${$store.getters.storage}/${user.avatar_path}`"
            :lazy-src="`${$store.getters.storage}/avatars/default.jpg`"
            :alt="user.name"
            class="rounded-xl"
            max-width="330"
            height="330"
          >
            <template v-slot:placeholder>
              <v-row class="fill-height ma-0" align="center" justify="center">
                <v-progress-circular
                  indeterminate
                  color="grey lighten-5"
                ></v-progress-circular>
              </v-row>
            </template>
          </v-img>
          <v-col cols="12" class="text-center" style="max-width: 330px">
            <input
              @change="fileUpload()"
              type="file"
              id="file"
              ref="file"
              style="display: none"
              accept="image/png, image/jpeg"
            />
            <v-btn fab x-small class="mx-2"
              ><v-icon @click="$refs.file.click()">mdi-download</v-icon></v-btn
            >
          </v-col>
        </v-col>
        <v-col cols="12" sm="8">
          <v-row>
            <v-col cols="4">
              <v-text-field
                v-model="user.name"
                :rules="requireRules"
                color="#ce6f61"
                label="Имя"
                dark
              ></v-text-field>
            </v-col>
            <v-col cols="4">
              <v-text-field
                v-model="user.surname"
                :rules="requireRules"
                color="#ce6f61"
                label="Фамилия"
                dark
              ></v-text-field>
            </v-col>
            <v-col cols="4">
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
                    label="Дата рождения"
                    readonly
                    dark
                    required
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
            <v-col cols="4">
              <v-text-field
                v-model="user.doc"
                color="#ce6f61"
                label="Номер документа"
                dark
              ></v-text-field>
            </v-col>
            <v-col cols="8"></v-col>
            <v-col cols="4">
              <v-text-field
                v-model="user.email"
                :rules="emailRules"
                color="#ce6f61"
                label="Email"
                dark
              ></v-text-field>
            </v-col>
            <v-col cols="12" class="text-right">
              <v-btn
                color="#ce6f61"
                outlined
                class="mx-2"
                @click="deleteProfile"
                >Удалить профиль</v-btn
              >
              <v-btn color="teal accent-3" class="mx-2" @click="save"
                >Сохранить</v-btn
              >
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-form>

    <v-row>
      <v-col cols="12" class="my-8">
        <ticket
          v-for="(tkt, i) in tkts"
          :key="`tkt${i}`"
          :tkt="tkt"
          :type="'show'"
        ></ticket>
      </v-col>
    </v-row>

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
import authenticator from '../plugins/authanticator'
import Ticket from '../components/Ticket'

export default {
  name: 'PageUser',

  components: {
    Ticket
  },

  data() {
    return {
      user: null,
      datepickerMenu: false,
      requireRules: [(v) => !!v || 'Поле обязательно!'],
      emailRules: [
        (v) => !!v || 'Email обязательно!',
        (v) => /.+@.+\..+/.test(v) || 'Email некорректный!'
      ],

      load: false,
      error: { show: false, message: 'Не удалось выполнить оперцию!' },
      success: { show: false, message: 'Операция успешно выполнена!' },

      tkts: []
    }
  },

  beforeMount() {
    this.user = this.$store.getters.user
  },

  async mounted() {
    this.load = true
    try {
      let resp = await this.$axios.get(`/Passanger/Tickets/${this.user.id}`)

      if (resp.data) {
        this.tkts = resp.data
      } else {
        this.error.show = true
      }

    } catch { this.error.show = true }
    finally { this.load = false }
  },

  methods: {
    async fileUpload() {

      let formData = new FormData()
      formData.append('file', this.$refs.file.files[0])
      
      if (this.$refs.file.files[0]) {
        this.load = true

        let config = {
          headers: {
            'content-type': 'multipart/form-data'
          }
        }

        let resp
        try {
          resp = await this.$axios.post(`/Passanger/LoadAvatar/${this.user.id}`, formData, config)
        } catch { this.error.show = true }
        finally { this.load = false }

        if (resp.data) {
          this.user.avatar_path = resp.data
          this.$store.commit('setUser', this.user)
          this.success.show = true

        } else {
          this.error.show = true
        }

        this.load = false
      }
    },
    async save() {
      if (this.$refs.form.validate()) {
        this.load = true
        try {
          let resp = await this.$axios.post(`/Passanger/Update/${this.user.id}`, this.user)

          if (resp.data) {
            this.$store.commit('setUser', this.user)
            this.success.show = true
          } else {
            this.error.show = true
          }

        } catch { this.error.show = true }
        finally { this.load = false }
      }
    },
    async deleteProfile() {
      this.load = true
      try {
        let resp = await this.$axios.post(`/Passanger/Delete/${this.user.id}`, this.user)
        if (resp.data) {
          authenticator.signOut()
          this.$router.push('/')
        } else {
          this.error.show = true
        }
      }
      catch {
        this.error.show = true
      }
      finally { this.load = false }
    }
  },

}
</script>

<style scoped>
</style>