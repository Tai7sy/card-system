<template>
  <div class='tabs-view-container' v-show="showTabs">
    <router-link class="tabs-view" v-for="tag in Array.from(visitedViews)" :to="tag.path" :key="tag.path">
      <el-tag :closable="true" :type="isActive(tag.path)?'primary':''" @close='closeViewTabs(tag,$event)'>
        {{tag.name}}
      </el-tag>
    </router-link>
  </div>
</template>

<script>
  export default {
    data () {
      return {
        availableWidth: 999,
        takeNumber: 6,
        showTabs: !this.$store.state.app.isMobile
      }
    },
    computed: {
      visitedViews () {
        return this.$store.state.app.visitedViews.slice(-this.takeNumber) // 最多6个
      },
    },
    watch: {
      $route (val, old) {
        if (this.visitedViews.length === 0) this.addViewTabs(old);
        this.addViewTabs();
      }
    },
    methods: {
      closeViewTabs (view, $event) {
        this.$store.dispatch('delVisitedViews', view);
        $event.preventDefault()
      },
      generateRoute (route = null) {
        if (route.matched[route.matched.length - 1].name) {
          return route.matched[route.matched.length - 1]
        }
        route.matched[0].path = '/';
        return route.matched[0]
      },
      addViewTabs (route = null) {
        this.$store.dispatch('addVisitedViews', this.generateRoute(route || this.$route))
      },
      isActive (path) {
        return path === this.$route.path
      },
      calcAvailableWidth () {
        const container = $('.navbar')[0];
        const a = $('.hamburger-container')[0];
        const b = $('.app-levelbar')[0];
        const usedWidth = a.offsetWidth + b.offsetWidth + 120;
        this.availableWidth = container.clientWidth - usedWidth;
        this.takeNumber = parseInt(this.availableWidth / 85);
        // console.log('availableWidth: ' + this.availableWidth);
        // console.log('takeNumber:     ' + this.takeNumber);
      },
      resizeHandler () {
        this.calcAvailableWidth();
      }
    },
    mounted () {
      window.addEventListener('resize', this.resizeHandler);
      const sidebarElm = document.getElementById('sidebar-container');
      if (sidebarElm) {
        sidebarElm.addEventListener('transitionend', this.resizeHandler);
      }
    },
    beforeDestroy () {
      window.removeEventListener('resize', this.resizeHandler);
      const sidebarElm = document.getElementById('sidebar-container');
      if (sidebarElm) {
        sidebarElm.removeEventListener('transitionend', this.resizeHandler);
      }
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .tabs-view-container {
    display: inline-block;
    vertical-align: top;
    margin-left: 10px;
    position: absolute;
    height: 50px;
    white-space: nowrap;
    .tabs-view {
      margin-left: 10px;
    }
  }
</style>
