<template>
  <div class="content-wrapper">
    <p style="margin-top: 0">这个功能可能不会上了? 看Star喽</p>
    <p>
      <mu-raised-button label="GITHUB" href="https://github.com/Tai7sy/card-system" class="gayhub" target="_blank">
        <img class="icon" src="../../../assets/images/GitHub-Mark-64px.png"/>
      </mu-raised-button>
    </p>
  </div>
</template>

<script>
  import myFunc from '../../../utils/myfunc.js'

  export default {
    data () {
      return {
        a: 1
      }
    },
    mounted () {
      this.checkLogin();
    },
    methods: {
      checkLogin () {
        if (!myFunc.cookie.get('login')) {
          this.$router.push('/admin/login');
        }
      }
    }
  }
</script>

<style lang="less" scoped>

  .gayhub{
    .icon{
      display: inline-block;
      vertical-align: middle;
      margin-left: 12px;
      margin-right: 0;
      line-height: 1;
      width: 24px;
      height: 24px;
    }
  }
</style>
