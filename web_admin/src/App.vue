<template>
  <router-view></router-view>
</template>

<script>
  export default {
    name: 'app',
    mounted () {
      console.log('store:');
      console.log(this.$store)
    }
  }
  // 全局自定义的css样式
  import '~/styles/index.scss'
</script>
