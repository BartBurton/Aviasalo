<template>
  <transition name="slide">
    <v-btn
      v-scroll="scrolling"
      v-show="isScrolled"
      @click="up"
      fab
      fixed
      bottom
      right
      small
      color="#ce6f61"
    >
      <v-icon color="#00131a">mdi-chevron-up</v-icon>
    </v-btn>
  </transition>
</template>

<script>
export default {
  name: 'Scroller',
  data: () => ({
    isScrolled: false
  }),
  methods: {
    scrolling(e) {
      this.isScrolled = window.pageYOffset > 40
    },
    up() {
      this.$vuetify.goTo(0)
    }
  }
}
</script>

<style>
.slide-enter-active {
  transition: all .5s;
}
.slide-leave-active {
  transition: all .5s;
}
.slide-enter, .slide-leave-to{
  transform: translateY(20px);
  opacity: 0;
}
</style>

