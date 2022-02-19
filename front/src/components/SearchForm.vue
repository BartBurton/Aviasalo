<template>
  <v-sheet class="rounded-lg back my-10 pa-10">
    <v-form ref="search">
      <v-container>
        <v-row>
          <v-col>
            <v-combobox
              v-model="departure"
              :rules="departureRules"
              :items="departures"
              no-data-text="Рейсов нет"
              validate-on-blur
              open-on-clear
              color="#ce6f61"
              dark
              :menu-props="{
                offsetY: true,
                transition: 'scale-transition',
              }"
              label="Откуда"
              item-color="#00131a"
            ></v-combobox>
          </v-col>

          <v-col>
            <v-combobox
              v-model="arrival"
              :rules="arrivalRules"
              :items="arrivals"
              no-data-text="Рейсов нет"
              validate-on-blur
              open-on-clear
              color="#ce6f61"
              dark
              :menu-props="{
                offsetY: true,
                transition: 'scale-transition',
              }"
              label="Куда"
              item-color="#00131a"
            ></v-combobox>
          </v-col>

          <v-col>
            <v-menu
              v-model="datepickerMenu"
              :close-on-content-click="false"
              transition="scale-transition"
              offset-y
              min-width="auto"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-text-field
                  v-model="date"
                  :rules="datepickerRules"
                  label="Дата"
                  validate-on-blur
                  readonly
                  dark
                  required
                  color="#ce6f61"
                  v-bind="attrs"
                  v-on="on"
                ></v-text-field>
              </template>
              <v-date-picker
                v-model="date"
                no-title
                scrollable
                locale="ru"
                color="#ce6f61"
                :min="new Date().toISOString().substr(0, 10)"
              >
                <v-spacer></v-spacer>
                <v-btn text color="#ce6f61" @click="datepickerMenu = false">
                  ОК
                </v-btn>
              </v-date-picker>
            </v-menu>
          </v-col>
        </v-row>
        <v-row>
          <v-col class="text-center">
            <v-btn color="#ce6f61" outlined elevation="10" @click="search">
              Найти
            </v-btn>
          </v-col>
        </v-row>
      </v-container>
    </v-form>
  </v-sheet>
</template>

<script>
export default {
  name: "SearchForm",

  data: () => ({
    departure: "",
    departures: [],
    departureRules: [(v) => !!v || "Место вылета обязательно!"],

    arrival: "",
    arrivals: [],
    arrivalRules: [(v) => !!v || "Место прибытия обязательно!"],

    date: (new Date()).toISOString().substring(0, 10),
    datepickerMenu: false,
    datepickerRules: [(v) => !!v || "Дата вылета обязательна!"],
  }),

  async mounted() {
    let query = `/Ticket/Cities`
    let resp = await this.$axios.get(query)
    try {
      this.departures = resp.data.departures
      this.arrivals = resp.data.arrivals
    } catch { }
  },

  methods: {
    search() {
      if (this.$refs.search.validate()) {
        this.$emit("search", {
          departure: this.departure,
          arrival: this.arrival,
          date: this.date,
        })
      }
    },
  },
};
</script>

<style scoped>
.back {
  position: relative;
  left: 50%;
  max-width: 1100px;
  background-image: linear-gradient(rgba(0, 19, 26, 0.6), rgba(0, 19, 26, 0.6)),
    url("/form-back.jpg");
  transform: translateX(-50%);
}
</style>

<style>
.v-menu__content::-webkit-scrollbar {
  width: 0px;
}
</style>



