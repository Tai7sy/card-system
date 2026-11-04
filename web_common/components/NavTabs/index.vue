<template>
  <div class='tabs-view-container' v-show="showTabs">
    <router-link class="tabs-view" v-for="tag in visitedViews" :to="tag.path" :key="tag.path">
      <el-tag :closable="true" :type="isActive(tag.path)?'primary':''"
              @close='closeViewTabs(tag,$event)'
              @click.prevent="$router.push(tag.path)">
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
        takeCount: 6,
        showTabs: !this.$store.state.app.isMobile
      }
    },
    computed: {
      visitedViews () {
        return this.$store.state.app.visitedViews.slice(-this.takeCount) // 最多6个
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
        if (this.visitedViews.length > 0) {
          const index = this.visitedViews.indexOf(view);
          console.log(index, this.visitedViews);
          if (index > 0) {
            this.$router.push(this.visitedViews[index - 1]) // 上一个
          } else {
            this.$router.push(this.visitedViews[this.visitedViews.length - 1]) // 最后一个
          }
        }
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

        const container = document.getElementsByClassName('navbar')[0];
        const a = document.getElementsByClassName('hamburger-container')[0];
        const b = document.getElementsByClassName('app-levelbar')[0];
        const usedWidth = a.offsetWidth + b.offsetWidth + 120;
        this.availableWidth = container.clientWidth - usedWidth;
        this.takeCount = parseInt(this.availableWidth / 100);
        
        
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

<style lang="scss">
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

    .tabs-view.router-link-active {
      .el-tag {
        border-bottom: none;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
        vertical-align: text-top;
        margin-top: 2px;
        background-color: #fff;
        color: #303133;
        border-color: #e6e6e6;

        .el-icon-close {
          color: #303133;

          &:hover {
            color: #fff;
            background-color: #FF4949;
          }
        }
      }
    }
  }
</style>
