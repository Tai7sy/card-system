<template>
  <mu-button @click="$emit('click')" :disabled="disabled">
    <span v-if="$slots.default"><slot></slot></span>
  </mu-button>
</template>

<script>
  export default {
    name: "ElButton",
    props:{
      disabled:{
        default: false
      }
    }
  }
</script>

<style>

</style>