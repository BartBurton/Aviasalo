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
        <router-view></router-view>
      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {
  name: 'PageAdmin',
  methods: {
    isEmpty(obj) {
      return obj == null || Object.keys(obj).length === 0;
    }
  }
}
</script>