<template>
  <div
    v-if="
      !isEmpty($store.getters.user) && $store.getters.user.role != undefined
    "
  >
    <v-row>
      <v-col>
        <h1 class="text-color-default">{{ $store.getters.user.name }}</h1>
      </v-col>
    </v-row>

    <v-row>
      <v-col>
        <v-card elevation="5">
          <v-tabs
            v-model="tabs"
            background-color="#00131a"
            color="#ce6f61"
            centered
            show-arrows
            dark
            fixed-tabs
          >
            <v-tab
              v-for="(item, i) in items"
              link
              :to="item.link"
              :key="`tab${i}`"
              >{{ item.label }}</v-tab
            >
          </v-tabs>
          <v-container class="pa-8" style="position: relative">
            <transition name="router-editor">
              <router-view />
            </transition>
          </v-container>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {
  name: 'PageMaker',
  data() {
    return {
      tabs: null,
      items: [
        { label: 'Билеты', link: { name: 'TicketsEditor' }, forMaker: true },
        { label: 'Авиакомпании', link: { name: 'AircompaniesEditor' }, forMaker: true },
        { label: 'Менеджеры', link: { name: 'MakersEditor' }, forMaker: false },
        { label: 'Пассажиры', link: { name: 'PassangersOserver' }, forMaker: false },
      ],
    }
  },

  async mounted() {
    let isAdmin = this.$store.getters.user.role === 'admin'
    this.items = this.items.filter(e => e.forMaker || isAdmin)
  },


  methods: {
    isEmpty(obj) {
      return obj == null || Object.keys(obj).length === 0;
    }
  }
}
</script>

<style>
.tabs {
  background-color: #00131a !important;
  color: #fff;
}
</style>

