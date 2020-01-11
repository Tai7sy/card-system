<template>
  <el-menu class="navbar" mode="horizontal">
    <hamburger class="hamburger-container" :toggleClick="toggleSideBar" :isActive="sidebar.opened"></hamburger>
    <level-bar></level-bar>
    <nav-tabs></nav-tabs>
    <error-log v-if="log.length>0" class="errLog-container" :logsList="log"></error-log>

    <div class="toolbar">
      <el-dropdown class="toolbar-icon" trigger="click" :show-timeout="0" :hide-timeout="0">
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
          <el-dropdown-item divided>
          <span @click="logout" style="display:block;">
            退出<icon name="sign-out-alt"/>
          </span>
          </el-dropdown-item>
        </el-dropdown-menu>
      </el-dropdown>
    </div>
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
    data() {
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
      toggleSideBar() {
        this.$store.dispatch('ToggleSideBar')
      },
      logout() {
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

    .toolbar {
      float: right;
      display: inline-block;
      margin-right: 8px;
      height: 48px;
      padding: 8px 0;

      .toolbar-icon {
        margin-right: 8px;
        cursor: pointer;
        width: 38px;
        height: 38px;
        overflow: hidden;

        &:last-child {
          margin-right: 0;
        }
      }

      .user-avatar {
        width: 100%;
        height: 100%;
        padding: 4px;
        border-radius: 2px;
      }
    }

  }

  .user-dropdown {
    transform: none !important;
    transition: none !important;
    margin-top: 4px !important;
    padding: 8px 0;
    min-width: 200px;
    // box-shadow: 0 2px 6px 0 rgba(0, 0, 0, 0.08);
    // border: 1px solid #e0e0e0;
    // border-radius: 2px !important;
    // background: #fff !important;

    svg {
      float: right;
      display: inline-block;
      margin: 12px 0;
      color: #cfd8dc;
      min-width: 16px;
      text-align: center;
      width: 12px;
      height: 12px;
    }

    .el-dropdown-menu__item {
      font-size: 13px;
    }

    a:hover {
      text-decoration: unset;
    }

    // .el-dropdown-menu__item:focus, .el-dropdown-menu__item:not(.is-disabled):hover {
    //   background: #fafafa;
    //   color: #263238;
    // }
  }
</style>



