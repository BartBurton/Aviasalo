<template>
  <v-app>
    <v-main class="back-color-default">
      <home-profile></home-profile>
      <v-container class="py-16 px-0" style="position:relative">
        <transition name="router">
          <router-view />
        </transition>
      </v-container>
      <scroller></scroller>
    </v-main>
  </v-app>
</template>

<script>
import Scroller from "./components/Scroller"
import HomeProfile from "./components/HomeProfile"

export default {
  name: 'App',
  components: {
    Scroller, HomeProfile
  },
}
</script>

<style>
.router-enter-active,
.router-leave-active {
  position: absolute;
  width: 100%;
  transition: all 0.3s;
}
.router-enter {
  opacity: 0;
  transform: translateY(-50px);
}
.router-leave-to {
  opacity: 0;
  transform: translateY(100px);
}

.back-color-default {
  background-color: #00131a !important;
}
.text {
  font-family: Roboto, sans-serif;
}
.text-color-default {
  color: #ffffff;
}
.text-color-important {
  color: #ce6f61;
}
</style>

