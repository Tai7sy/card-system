<template>
  <el-menu class="navbar" mode="horizontal">
    <hamburger class="hamburger-container" :toggleClick="toggleSideBar" :isActive="sidebar.opened"></hamburger>
    <level-bar></level-bar>
    <nav-tabs></nav-tabs>
    <error-log v-if="log.length>0" class="errLog-container" :logsList="log"></error-log>
    <el-dropdown class="avatar-container" trigger="click" :show-timeout="0" :hide-timeout="0">
      <div class="avatar-wrapper">
        <img class="user-avatar" :src="require('~/assets/header.png')">
        <i class="el-icon-caret-bottom"></i>
      </div>
      <el-dropdown-menu class="user-dropdown" slot="dropdown">
        <router-link class='inlineBlock' to="/admin/system/set">
          <el-dropdown-item>网站设置</el-dropdown-item>
        </router-link>
        <router-link class='inlineBlock' to="/admin/system/security">
          <el-dropdown-item>修改密码</el-dropdown-item>
        </router-link>
        <el-dropdown-item divided><span @click="logout" style="display:block;">退出</span></el-dropdown-item>
      </el-dropdown-menu>
    </el-dropdown>
  </el-menu>
</template>

<script>
  import { mapGetters } from 'vuex';
  import LevelBar from './Levelbar';
  import NavTabs from '~/components/NavTabs';
  import Hamburger from '~/components/Hamburger';
  import ErrorLog from '~/components/ErrLog';
  import errLogStore from '../../store/errLog';

  export default {
    components: {
      LevelBar,
      NavTabs,
      Hamburger,
      ErrorLog
    },
    data () {
      return {
        log: errLogStore.state.errLog
      }
    },
    computed: {
      ...mapGetters([
        'sidebar'
      ])
    },
    methods: {
      toggleSideBar () {
        this.$store.dispatch('ToggleSideBar')
      },
      logout () {
        this.$store.dispatch('Logout').then(() => {
          location.reload(); // 为了重新实例化vue-router对象 避免bug
        });
      }
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .navbar {
    height: 50px;
    line-height: 50px;
    border-radius: 0 !important;

    .hamburger-container {
      line-height: 58px;
      height: 50px;
      float: left;
      padding: 0 10px;
      outline: none;
    }

    .errLog-container {
      display: inline-block;
      position: absolute;
      right: 150px;
    }

    .screen-full {
      position: absolute;
      right: 90px;
      top: 16px;
      color: red;
    }

    .avatar-container {
      height: 50px;
      display: inline-block;
      position: absolute;
      right: 35px;

      .avatar-wrapper {
        cursor: pointer;
        margin-top: 5px;
        position: relative;

        .user-avatar {
          width: 40px;
          height: 40px;
          border-radius: 10px;
        }

        .el-icon-caret-bottom {
          position: absolute;
          right: -20px;
          top: 25px;
          font-size: 12px;
        }
      }
    }
  }
  .user-dropdown {
    transform: none !important;
    transition: none !important;
  }
</style>



