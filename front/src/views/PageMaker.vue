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
            center-active
            dark
          >
            <v-tab v-for="(item, i) in items" :key="`tab${i}`">{{
              item.label
            }}</v-tab>

            <v-tabs-items v-model="tabs" class="tabs">
              
              <v-tab-item>
                <tickets-editor
                  :isAdmin="isAdmin"
                  key="tickets-editor"
                ></tickets-editor>
              </v-tab-item>

              <v-tab-item>
                <v-container>
                  <v-row>
                    <v-col>
                      <aircompanies-editor
                        key="aircompanies-editor"
                      ></aircompanies-editor>
                    </v-col>
                  </v-row>
                </v-container>
              </v-tab-item>

              <v-tab-item>
                <v-container>
                  <v-row>
                    <v-col>
                      <h1>Passangers editor</h1>
                    </v-col>
                  </v-row>
                </v-container>
              </v-tab-item>

              <v-tab-item>
                <v-container>
                  <v-row>
                    <v-col>
                      <h1>Makers editor</h1>
                    </v-col>
                  </v-row>
                </v-container>
              </v-tab-item>

            </v-tabs-items>
          </v-tabs>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import TicketsEditor from '../components/maker/TicketsEditor'
import AircompaniesEditor from '../components/maker/AircompaniesEditor'

export default {
  name: 'PageMaker',
  components: { TicketsEditor, AircompaniesEditor },
  data() {
    return {
      tabs: null,
      items: [
        { label: 'Билеты', forMaker: true },
        { label: 'Авиакомпании', forMaker: false },
        { label: 'Пассажиры', forMaker: false },
        { label: 'Менеджеры', forMaker: false },
      ],
      isAdmin: false,
    }
  },

  async mounted() {
    this.isAdmin = this.$store.getters.user.role === 'admin'
    this.items = this.items.filter(e => e.forMaker || this.isAdmin)
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

