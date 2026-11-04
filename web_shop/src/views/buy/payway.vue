<template>
  <div class="ways-container">
    <div v-for="way in pays" class="payway" :key="way.id"
         :class="{checked: active === way}"
         @click="active = way">
      <img :src="way.img" :alt="way.name"/>
      <p>{{way.name}}</p>
    </div>
  </div>
</template>

<script>
  export default {
    name: "payway",
    props: {
      value: {},
    },
    data () {
      return {
        pays: window.config.pays,
        active: null
      }
    },
    mounted () {
      this.active = this.pays[0];
    },
    watch: {
      value (val) {
        this.active = val
      },
      active (val) {
        this.$emit('input', val)
      }
    }
  }
</script>

<style lang="scss" scoped>
  .ways-container {
    white-space: normal;
  }

  .payway {
    position: relative;
    display: inline-block;
    margin: 4px 12px 4px 0;
    padding: 4px;
    cursor: pointer;
    border: 1px solid transparent;

    img {
      height: 48px;
    }

    p {
      line-height: 14px;
      text-align: center;
      margin: 0;
    }

    &.checked {
      border: 1px solid #03a9f4;

      &:after {
        content: '';
        position: absolute;
        box-sizing: border-box;
        right: 0;
        bottom: 0;
        border-bottom: 12px solid #03a9f4;
        border-left: 12px solid transparent;
      }
    }

    &:last-child {
      margin-right: 0;
    }

    /*
    &:not(.checked) {
      filter: grayscale(90%);

      p {
        color: #888888;
      }
    }
    */
  }
</style>
