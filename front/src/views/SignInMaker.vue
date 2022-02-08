<template>
  <div>
    <v-row justify="center">
      <v-col cols="12" class="text-center">
        <h1 class="text text-color-default">ВХОД АДМИНА/МЕНЕДЖЕРА</h1>
        <v-divider class="mb-16 mt-5" dark></v-divider>
      </v-col>
      <v-col cols="4">
        <v-form ref="signin">
          <v-row>
            <v-col cols="12">
              <v-text-field
                v-model="name"
                :rules="nameRules"
                dark
                color="#ce6f61"
                label="Имя"
                outlined
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-text-field
                v-model="password"
                :rules="passwordRules"
                :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                :type="showPassword ? 'text' : 'password'"
                dark
                color="#ce6f61"
                label="Пароль"
                outlined
                @click:append="showPassword = !showPassword"
              ></v-text-field>
            </v-col>
            <v-col cols="12" class="text-center">
              <v-btn color="#ce6f61" style="color: #00131a" @click="signIn">
                <span v-if="!load">Войти</span>
                <span v-else>
                  <div class="lds-ring">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div></div
                ></span>
              </v-btn>
            </v-col>
          </v-row>
        </v-form>
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
  </div>
</template>

<script>
import authenticator from '../plugins/authanticator'

export default {
  name: 'SignInMaker',

  props: {
    next: {
      type: String,
      default: '/maker',
    },
  },
  data() {
    return {
      name: '',
      nameRules: [
        (v) => !!v || 'Имя обязательно!',
      ],
      password: '',
      passwordRules: [(v) => !!v || 'Пароль обязательно!'],
      showPassword: false,

      load: false,
      error: { show: false, message: 'Не удалось войти!' },
    }
  },

  mounted() {
    document.addEventListener('keypress', (event) => {
      const keyName = event.key;
      if (keyName == 'Enter') {
        this.signIn()
      }
    });
  },

  methods: {
    async signIn() {
      if (this.$refs.signin.validate()) {
        this.load = true

        let isSignIn = await authenticator.signInMaker({
          name: this.name,
          password: this.password
        })

        if (isSignIn) {
          this.$router.push(this.next)
        } else {
          this.error.show = true
        }

        this.load = false
      }
    }
  },

}
</script>

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