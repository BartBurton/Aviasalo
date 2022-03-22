<template>
  <div>
    <v-row v-if="isMounted">
      <v-col class="text-center pa-16">
        <v-progress-circular
          indeterminate
          color="#ce6f61"
          size="70"
        ></v-progress-circular>
      </v-col>
    </v-row>

    <v-form v-else ref="arcForm">
      <input
        @change="arcFileUpload()"
        type="file"
        id="arcFile"
        ref="arcFile"
        style="display: none"
        accept="image/png, image/jpeg"
      />

      <v-row>
        <v-col class="text-right">
          <v-btn
            color="teal accent-3"
            small
            fab
            @click="
              doViaCheckAccess(() => {
                addArc();
              })
            "
            ><v-icon>mdi-plus</v-icon></v-btn
          >
        </v-col>
      </v-row>

      <v-row v-for="(arc, i) in aircompanies" :key="`arc-${i}`">
        <v-col>
          <v-card :class="i % 2 == 0 ? 'item0' : 'item1'" class="pa-2">
            <v-card-title>
              <v-row align="center">
                <v-col cols="2">
                  <v-hover v-slot="{ hover }">
                    <v-img
                      class="rounded-lg file-uploader"
                      @click="
                        doViaCheckAccess(() => {
                          loadArcFileHandler(arc);
                        })
                      "
                      :src="`${$store.getters.storage}/${arc.image_path}`"
                      :lazy-src="`${$store.getters.storage}/previews/lazy.jpg`"
                      @error="errorLoadArcImg(arc)"
                      max-width="128"
                      min-height="46"
                      :gradient="
                        hover
                          ? '0deg, rgba(0,19,26,1) 0%, rgba(0,19,26,0.5511122417717087) 60%, rgba(0,19,26,0.3046136423319328) 100%'
                          : ''
                      "
                    >
                      <template v-slot:placeholder>
                        <v-row
                          class="fill-height ma-0"
                          align="center"
                          justify="center"
                        >
                          <v-progress-circular
                            indeterminate
                            color="grey lighten-5"
                          ></v-progress-circular>
                        </v-row> </template
                    ></v-img>
                  </v-hover>
                </v-col>

                <v-col cols="3" class="text-center">
                  <v-text-field
                    v-model="arc.name"
                    :rules="requireRules"
                    color="#ce6f61"
                    label="Название"
                    dark
                  ></v-text-field>
                </v-col>

                <v-col cols="7" class="text-right">
                  <v-btn
                    color="red darken-1"
                    class="ma-2"
                    outlined
                    x-small
                    fab
                    @click="
                      doViaCheckAccess(() => {
                        deleteArc(arc);
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
                        saveArc(arc);
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
      @allowed="allow"
      @denied="deny"
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
  name: 'AircompaniesEditor',
  components: { AccessChecker },
  mixins: [AccessCheckerMixin],
  data() {
    return {
      aircompanies: [],

      requireRules: [(v) => !!v || 'Поле обязательно!'],
      arcUpdateFile: null,

      load: false,
      isMounted: false,
      error: { show: false, message: 'Не удалось выполнить оперцию!' },
      success: { show: false, message: 'Операция успешно выполнена!' },
    }
  },

  async mounted() {
    this.isMounted = true
    try {
      let resp = await this.$axios.get(`/Aircompany/All`)
      if (resp.data) { this.aircompanies = resp.data }

      else { this.error.show = true }

    } catch { this.error.show = true }
    finally { this.isMounted = false }
  },

  methods: {
    errorLoadArcImg(arc) {
      arc.image_path = `previews/lazy.jpg`
    },
    loadArcFileHandler(arc) {
      this.arcUpdateFile = arc
      this.$refs.arcFile.click()
    },
    async arcFileUpload() {
      let formData = new FormData()
      formData.append('file', this.$refs.arcFile.files[0])

      if (this.$refs.arcFile.files[0]) {
        this.load = true

        let config = {
          headers: {
            'content-type': 'multipart/form-data'
          }
        }

        let resp
        try {
          resp = await this.$axios.post(`/Aircompany/LoadLogo/${this.arcUpdateFile.id}`, formData, config)
          if (resp.data) {
            this.arcUpdateFile.image_path = resp.data
            this.success.show = true
          } else {
            this.error.show = true
          }
        } catch { this.error.show = true }
        finally { this.load = false }
      }
    },

    async addArc() {
      this.load = true

      let query = `/Aircompany/Create/`
      let arc = {
        name: 'Aircompany',
        image_path: 'logo'
      }

      let resp
      try {
        resp = await this.$axios.post(query, arc)

        if (resp.data) {
          arc['id'] = resp.data.id
          this.aircompanies.unshift(arc)

          this.success.show = true
        } else {
          this.error.show = true
        }

      } catch { this.error.show = true }
      finally { this.load = false }
    },

    async saveArc(arc) {
      if (this.$refs.arcForm.validate()) {
        this.load = true

        let query = `/Aircompany/Update/${arc.id}`
        let data = {
          name: arc.name,
        }

        let resp
        try {
          resp = await this.$axios.post(query, data)
          console.log(resp)
          if (resp.data) {
            this.success.show = true
          } else {
            this.error.show = true
          }

        } catch { this.error.show = true }
        finally { this.load = false }
      }
    },

    async deleteArc(arc) {
      this.load = true

      let query = `/Aircompany/Delete/${arc.id}`

      let resp
      try {
        resp = await this.$axios.post(query)

        if (resp.data) {
          this.aircompanies.splice(this.aircompanies.indexOf(arc), 1)
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
.file-uploader {
  cursor: pointer;
}
</style>