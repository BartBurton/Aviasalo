<template>
  <div>
    <v-dialog
      v-model="check"
      :width="$vuetify.breakpoint.lg ? '40%' : '80%'"
      persistent
    >
      <v-form ref="accessForm">
        <v-card height="100%">
          <v-card-title>
            <v-row justify="center">
              <v-col cols="10" class="mt-5 text-center">
                <h3 class="text-color-default">Подтвердить права</h3>
              </v-col>
            </v-row>
          </v-card-title>

          <v-card-text>
            <v-row justify="center">
              <v-col cols="10" class="mt-5">
                <v-text-field
                  v-model="password"
                  type="password"
                  :rules="requireRules"
                  color="#ce6f61"
                  label="Пароль"
                  outlined
                  dark
                ></v-text-field>
              </v-col>
            </v-row>
          </v-card-text>

          <v-divider></v-divider>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn
              color="#ce6f61"
              class="ma-2"
              outlined
              @click="$emit('denied')"
              >Отмена</v-btn
            >
            <v-btn
              color="#ce6f61"
              class="ma-2"
              style="color: #00131a"
              @click="checkAccess"
            >
              <span>Подтвердить</span>
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-form>
    </v-dialog>

    <v-snackbar color="#00131a" v-model="lock" elevation="10" class="pa-5">
      <span :style="`color:${'#ce6f61'};`"> Отказано в доступе </span>
      <template v-slot:action="{ attrs }">
        <v-btn
          color="#ce6f61"
          fab
          text
          x-small
          v-bind="attrs"
          @click="lock = false"
        >
          <v-icon> mdi-lock </v-icon>
        </v-btn>
      </template>
    </v-snackbar>
  </div>
</template>

<script>
export default {
  name: 'AccessChecker',
  props: {
    value: { type: Boolean, default: false },
    duration: { type: Number, default: 60000 }
  },
  data: () => ({
    requireRules: [(v) => !!v || 'Поле обязательно!'],
    check: false,

    password: null,

    session: null,

    lock: false
  }),
  watch: {
    value: function (oldVal, newVal) {
      if (this.value) {
        if (!this.$store.getters.isChecked) {
          this.check = true
        } else {
          this.updateSession()
          this.$emit('allowed')
        }
      } else {
        this.check = false
      }
    }
  },
  methods: {
    checkAccess() {
      if (this.$refs.accessForm.validate()) {
        if (this.password === this.$store.getters.user.password) {
          this.updateSession()
          this.$emit('allowed')
        } else {
          this.lock = true
          this.$emit('denied')
        }

        this.$refs.accessForm.reset()
        this.password = null
      }
    },
    updateSession() {
      clearTimeout(this.session)
      this.session = setTimeout(() => {
        this.$emit('dropSession')
        this.$store.commit('setChecked', false)
      }, this.duration)
      this.$store.commit('setChecked', true)
    }
  }
}
</script>   

<style scoped>
</style>