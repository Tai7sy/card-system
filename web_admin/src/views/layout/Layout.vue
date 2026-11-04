<template>
  <div class="app-wrapper" :class="{hideSidebar:!sidebarOpen,showSidebar:sidebarOpen}">
    <sidebar ref="side-bar" class="sidebar-container" id="sidebar-container" @transitionend="resizeHandler"/>
    <!--:style="{overflow:sidebarOpen?'auto':'initial'}"-->

    <div class="main-container">
      <div :style="{width:mainWidth}">
        <navbar ref="nav-bar"/>
        <app-main/>
      </div>
    </div>
    <lic/>
  </div>
</template>

<script>
  import { AppMain, Navbar, Sidebar } from '.';
  import Lic from "@/views/about/lic";

  export default {
    name: 'layout',
    components: {
      Lic,
      Navbar,
      Sidebar,
      AppMain
    },
    data () {
      return {
        mainWidth: 'auto'
      }
    },
    computed: {
      sidebarOpen () {
        return this.$store.state.app.sidebar.opened;
      }
    },
    watch: {
      sidebarOpen () {
        this.resizeHandler({ type: 'sidebarOpen', propertyName: 'width' });
      }
    },
    mounted () {
      this.reCalcMainWidth();
      window.addEventListener('resize', this.resizeHandler);
    },
    beforeDestroy () {
      window.removeEventListener('resize', this.resizeHandler);
    },
    methods: {
      resizeHandler (e) {
        
        if (!e || (e.type !== 'resize' && e.propertyName !== 'width')) return;
        this.reCalcMainWidth();
        window.bus.$emit('app-resize');
      },
      reCalcMainWidth () {
        if (!this.$store.state.app.isMobile) return;
        if (document.body.clientWidth !== this.oldClientWidth) {
          this.oldClientWidth = document.body.clientWidth;
          this.mainWidth = (document.body.clientWidth - this.$refs['side-bar'].$el.clientWidth) + 'px';
        }
      }
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  @import "~/styles/mixin.scss";

  .app-wrapper {
    @include clearfix;
    position: relative;
    height: 100%;
    width: 100%;
    display: table-row;
    &.hideSidebar {
      .sidebar-container {
        width: 36px;
      }
      .main-container {
        margin-left: 36px;
      }
    }
    &.showSidebar {
      .sidebar-container {
        overflow-x: hidden !important;
      }
    }
    .sidebar-container {
      display: table-cell;
      transition: width 0.28s ease-out;
      width: 180px;
      height: 100%;
      position: fixed;
      top: 0;
      bottom: 0;
      left: 0;
      z-index: 1010;
      
      
    }
    .main-container {
      min-height: 100%;
      transition: margin-left 0.28s ease-out;
      margin-left: 180px;
      position: relative;
      .main-content {
        width: 375px;
      }
    }
  }
</style>
