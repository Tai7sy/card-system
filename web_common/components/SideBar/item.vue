<template>
  <div class='menu-wrapper'>
    <template v-for="item in routes">

      <router-link v-if="!item.hidden&&item.noDropdown&&item.children.length>0"
                   :to="item.path + '/' + item.children[0].path">
        <el-menu-item :index="item.path + '/' + item.children[0].path" class='submenu-title-noDropdown'>
          <icon v-if='item.icon' :name="item.icon"/>
          <span class="menu-title">{{item.children[0].name}}</span>
        </el-menu-item>
      </router-link>

      <el-submenu :index="item.name" v-if="!item.noDropdown&&!item.hidden">
        <template slot="title">
          <icon v-if='item.icon' :name="item.icon"/>
          <span class="menu-title">{{item.name}}</span>
        </template>
        <template v-for="child in item.children" v-if='!child.hidden'>

          <item v-if='child.children&&child.children.length>0' class='nest-menu'
                        :routes='[child]'></item>

          <router-link v-else :to="item.path + '/' + child.path">
            <el-menu-item :index="item.path + '/' + child.path" v-if="!child.hide">
              <icon v-if='child.icon' :name="child.icon"/>
              <span class="menu-title">{{child.name}}</span>
            </el-menu-item>
          </router-link>

        </template>

      </el-submenu>

    </template>
  </div>
</template>

<script>
  export default {
    name: 'item',
    props: {
      routes: {
        type: Array
      }
    }
  }
</script>

<style lang="scss" scoped>
  .menu-title {
    user-select: none;
    margin-left: 4px;
  }

  .fa-icon {
    line-height: 16px;
    vertical-align: middle;
  }
</style>

