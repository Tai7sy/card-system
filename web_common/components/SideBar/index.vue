<template>
  <div>
    <el-menu mode="vertical" :default-active="$route.path" :collapse="isCollapse">
      <item :routes='permission_routers'></item>
    </el-menu>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import item from './item';

  export default {
    components: { item },
    computed: {
      ...mapGetters([
        'permission_routers',
        'sidebar'
      ]),
      isCollapse () {
        return !this.sidebar.opened
      }
    }
  }
</script>

<style lang="scss" scoped>
  .el-menu {
    min-height: 100%;
  }
</style>

<style lang="scss">
  .el-menu--collapse .el-submenu .el-menu {
    z-index: 1001 !important;
  }
</style>
