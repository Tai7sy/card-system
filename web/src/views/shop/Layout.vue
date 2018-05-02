<template>
  <div>
    <div class="header">
      <h1>{{title}}</h1>
    </div>
    <div class="main">
      <div class="nav">
        <a :class="{active:activeTab==='buy'}" @click="handleTabChange('buy')">购买卡密</a>
        <a :class="{active:activeTab==='record'}" @click="handleTabChange('record')">查询记录</a>
      </div>
      <transition name="slide-fade">
        <router-view/>
      </transition>
      <footer>
        <p>
          © 2017
          <a href="https://github.com/Tai7sy/card-system" target="_blank">
            <img class="icon" src="../../assets/images/GitHub-Mark-64px.png"/>
          </a>
          <a href="https://his.cat" target="_blank">
            Code By Windy
          </a>
        </p>
      </footer>
    </div>
    <my-alert></my-alert>
  </div>

</template>
<script>
  import Vue from 'vue';
  import myAlert from '../../components/alert.vue'

  export default {
    components: {
      'my-alert': myAlert
    },
    data () {
      return {
        title: window.app_name || '发卡系统',
        activeTab: ''
      }
    },
    mounted () {
      this.activeTab = this.$route.path.indexOf('buy') > -1 ? 'buy' : 'record';
      Vue.prototype.$alert = this.alert;
    },
    methods: {
      alert (msg, title, callback, hasCancel) {
        window.vm.$emit('alert', {
          msg: msg,
          title: (title || '提示'),
          hasCancel: hasCancel,
          callback: callback
        })
      },
      handleTabChange (val) {
        this.activeTab = val;
        this.$router.push(val);
      }
    }
  }
</script>

<style lang="less" scoped>
  .slide-fade-enter-active {
    transition: all .5s;
  }

  .slide-fade-leave-active {
    transition: all .5s;
  }

  .slide-fade-enter, .slide-fade-leave-active {
    opacity: 0;
  }

  .slide-fade-enter {
    transform: translateX(100px);
  }

  .slide-fade-leave-active {
    position: absolute;
    transform: translateX(-100px);
  }

  .header {
    width: 100%;
    margin-top: 30px;
    text-align: center;
  }

  .nav {
    height: 40px;
    a {
      font-size: 16px;
      cursor: pointer;
      display: inline-block;
      margin: 12px 8px;
      color: black;
      transition: all 0.2s;
    }
    a.active {
      font-size: 16px;
      text-decoration: underline;
    }
  }

  .main {
    max-width: 700px;
    margin: 0 auto;
    padding-bottom: 100px;
  }

  footer {
    width: 700px;
    margin: 50px auto 0;
    height: 100px;
    padding: 0 8px;
    a {
      color: #ff4081;
      transition: all 0.4s;
    }
    a:hover {
      color: grey;
    }
    .icon{
      display: inline-block;
      vertical-align: middle;
      margin: -3px 6px 0;
      width: 16px;
      height: 16px;
      line-height: 1;
    }
  }
</style>
