<template>
  <transition name="header">
    <div class="header">
      <div class="px-1 py-3" style="display: inline">
        <v-btn to="/" fab small color="white" class="">
          <v-icon color="#00131a">mdi-magnify</v-icon>
        </v-btn>
      </div>

      <div class="px-4 py-3" style="display: inline">
        <v-btn
          v-if="isEmpty($store.getters.user)"
          to="/profile"
          fab
          small
          color="white"
        >
          <v-icon color="#00131a">mdi-account</v-icon>
        </v-btn>

        <v-menu v-else-if="$store.getters.user.role == undefined" rounded="lg">
          <template v-slot:activator="{ on, attrs }">
            <v-avatar size="40px" class="" v-bind="attrs" v-on="on">
              <v-img
                :src="`${$store.getters.storage}/${$store.getters.user.avatar_path}`"
                :lazy-src="`${$store.getters.storage}/avatars/default.jpg`"
                :alt="$store.getters.user.name"
              >
              </v-img>
            </v-avatar>
          </template>
          <v-list>
            <v-list-item to="/profile">
              <v-list-item-title>Профиль</v-list-item-title>
            </v-list-item>
            <v-list-item link @click="signOut">
              <v-list-item-title>Выход</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>

        <v-menu v-else-if="$store.getters.user.role == 'maker'" rounded="lg">
          <template v-slot:activator="{ on, attrs }">
            <v-btn v-bind="attrs" v-on="on" fab small color="white">
              <v-icon color="#00131a">mdi-pencil</v-icon>
            </v-btn>
          </template>
          <v-list>
            <v-list-item to="/maker">
              <v-list-item-title>Панель менеджера</v-list-item-title>
            </v-list-item>
            <v-list-item link @click="signOut">
              <v-list-item-title>Выход</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>

        <v-menu v-else-if="$store.getters.user.role == 'admin'" rounded="lg">
          <template v-slot:activator="{ on, attrs }">
            <v-btn v-bind="attrs" v-on="on" fab small color="white">
              <v-icon color="#00131a">mdi-lock-open</v-icon>
            </v-btn>
          </template>
          <v-list>
            <v-list-item to="/admin">
              <v-list-item-title>Панель админа</v-list-item-title>
            </v-list-item>
            <v-list-item link @click="signOut">
              <v-list-item-title>Выход</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </div>
    </div>
  </transition>
</template>

<script>
import authenticator from '../plugins/authanticator'
export default {
  name: 'HomeProfile',
  data: () => ({}),
  methods: {
    signOut() {
      authenticator.signOut()
      this.$router.push('/')
    },
    isEmpty(obj) {
      return obj == null || Object.keys(obj).length === 0;
    }
  }
}
</script>

<style>
.header {
  position: fixed;
  z-index: 1;
  top: 16px;
  right: 0px;
}

.header-enter-active .header-leave-active {
  transition: all 0.5s;
}

.header-enter,
.slide-leave-to {
  transform: translateX(20px);
  opacity: 0;
}
</style>

