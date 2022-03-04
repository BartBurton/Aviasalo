<template>
  <div>
    <v-form ref="mkrForm">
      <v-row>
        <v-col class="text-right">
          <v-btn
            color="teal accent-3"
            small
            fab
            @click="
              doViaCheckAccess(() => {
                addMkr();
              })
            "
            ><v-icon>mdi-plus</v-icon></v-btn
          >
        </v-col>
      </v-row>

      <v-row v-for="(mkr, i) in makers" :key="`mkr-${i}`">
        <v-col>
          <v-card :class="i % 2 == 0 ? 'item0' : 'item1'" class="pa-2">
            <v-card-title>
              <v-row align="center">
                <v-col cols="3" class="text-center">
                  <v-text-field
                    v-model="mkr.name"
                    :rules="requireRules"
                    color="#ce6f61"
                    label="Имя"
                    dark
                  ></v-text-field>
                </v-col>

                <v-col cols="3" class="text-center">
                  <v-text-field
                    v-model="mkr.password"
                    :rules="requireRules"
                    color="#ce6f61"
                    label="Пароль"
                    dark
                    :append-icon="mkr.showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                    :type="mkr.showPassword ? 'text' : 'password'"
                    @click:append="tryShowPasswords(mkr)"
                  ></v-text-field>
                </v-col>

                <v-col cols="2" class="text-center">
                  <v-select
                    v-model="mkr.role"
                    :rules="requireRules"
                    :items="roles"
                    no-data-text="Ролей нет"
                    validate-on-blur
                    filled
                    open-on-clear
                    :background-color="
                      mkr.role == 'maker' ? `#ce7061a1` : `#f0cc6093`
                    "
                    color="#ce6f61"
                    dark
                    :menu-props="{
                      offsetY: true,
                      transition: 'scale-transition',
                    }"
                    label="Роль"
                    item-color="#00131a"
                  ></v-select>
                </v-col>

                <v-col cols="2" class="text-center">
                  <v-checkbox
                    v-model="mkr.is_active"
                    label="Активен"
                    color="#ce6f61"
                    dark
                    class="mt-0"
                  ></v-checkbox>
                </v-col>

                <v-col cols="2" class="text-right pt-0">
                  <v-btn
                    color="red darken-1"
                    class="ma-2"
                    outlined
                    x-small
                    fab
                    @click="
                      doViaCheckAccess(() => {
                        deleteMkr(mkr);
                      })
                    "
                    ><v-icon>mdi-delete-outline</v-icon></v-btn
                  >
                  <v-btn
                    color="teal accent-3"
                    class="ma-2"
                    x-small
                    fab
                    @click="
                      doViaCheckAccess(() => {
                        saveMkr(mkr);
                      })
                    "
                    ><v-icon>mdi-content-save </v-icon></v-btn
                  >
                </v-col>
              </v-row>
            </v-card-title>
          </v-card>
        </v-col>
      </v-row>
    </v-form>

    <access-checker
      v-model="check"
      :duration="30000"
      @allowed="allow"
      @denied="deny"
      @dropSession="
        () => {
          hidePasswords();
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
import AccessChecker from './AccessChecker.vue'
import AccessCheckerMixin from '../../mixins/AccessCheckerMixin.js'
export default {
  name: 'MakersEditor',
  components: { AccessChecker },
  mixins: [AccessCheckerMixin],
  data: () => ({
    makers: [],
    roles: null,

    requireRules: [(v) => !!v || 'Поле обязательно!'],

    load: false,
    error: { show: false, message: 'Не удалось выполнить оперцию!' },
    success: { show: false, message: 'Операция успешно выполнена!' },
  }),
  async mounted() {
    this.load = true
    try {
      let resp = await this.$axios.get(`/Admin/AllMakers`)
      if (resp.data) {
        this.makers = resp.data.map(e => {
          e['showPassword'] = false
          return e
        })
        this.roles = Array.from(new Set(this.makers.map(e => e.role)))
      }
      else { this.error.show = true }

    } catch { this.error.show = true }
    finally { this.load = false }
  },
  methods: {
    tryShowPasswords(mkr) {
      if (mkr.showPassword) mkr.showPassword = false
      else this.doViaCheckAccess(() => {
        mkr.showPassword = true
      }, () => {
        mkr.showPassword = false
      })
    },
    hidePasswords() {
      this.makers = this.makers.map(e => {
        e.showPassword = false
        return e
      })
    },


    async addMkr() {
      this.load = true

      let query = `/Admin/CreateMaker/`
      let mkr = {
        name: 'Менеджер',
        password: '1',
        role: 'maker',
        is_active: false,
        showPassword: true
      }

      let resp
      try {
        resp = await this.$axios.post(query, mkr)

        if (resp.data) {
          mkr['id'] = resp.data.id
          this.makers.unshift(mkr)

          this.success.show = true
        } else {
          this.error.show = true
        }

      } catch { this.error.show = true }
      finally { this.load = false }
    },

    async saveMkr(mkr) {
      if (this.$refs.mkrForm.validate()) {
        this.load = true

        let query = `/Admin/UpdateMaker/${mkr.id}`
        let data = {
          name: mkr.name,
          password: mkr.password,
          role: mkr.role,
          is_active: mkr.is_active,
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

    async deleteMkr(mkr) {
      this.load = true

      let query = `/Admin/DeleteMaker/${mkr.id}`

      let resp
      try {
        resp = await this.$axios.post(query)

        if (resp.data) {
          this.makers.splice(this.makers.indexOf(mkr), 1)
          this.success.show = true
        } else {
          this.error.show = true
        }
      } catch { this.error.show = true }
      finally { this.load = false }
    },
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
</style>