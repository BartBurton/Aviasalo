<template>
  <div class="sd-container" :style="`min-width: ${width}px;`">
    <div class="sd-sub sd-top">
      <slot name="top"></slot>
    </div>
    <div class="sd-sub sd-center"><slot></slot></div>
    <div class="sd-sub sd-bottom">
      <slot name="bottom"></slot>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ColInfo',
  props: {
    width: {
      type: Number,
      default: 150,
    },
  },
}
</script>

<style scoped>
.sd-container {
  position: relative;
  min-width: 150px;
  height: 100px;
}

.sd-sub {
  position: absolute;
  left: 50%;
  transform: translate(-50%, -50%);
}

.sd-top {
  top: 20%;
}

.sd-center {
  top: 50%;
}

.sd-bottom {
  top: 80%;
}
</style>