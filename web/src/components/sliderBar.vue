<template>
  <mu-drawer @close="handleClose" :open="open" :docked="docked" class="app-drawer" :zDepth="1">
    <mu-appbar class="my-nav-appbar" :zDepth="0">
      <a class="my-appbar-title" @click="handleMenuChange('#/index')" href="#/index"
         style="display:inline-block;">菜单</a>
      <span slot="right">Ver: {{version}}&nbsp;</span>
    </mu-appbar>
    <mu-divider/>
    <mu-list @change="handleMenuChange" :value="chooseMenu">
      <mu-list-item value="#/admin/dashboard" title="首页" @click="handleMenuIndexClick"/>
      <mu-list-item title="功能区" toggleNested>
        <mu-list-item slot="nested" title="分类管理" value="#/admin/group"/>
        <mu-list-item slot="nested" title="商品管理" value="#/admin/good"/>
        <mu-list-item slot="nested" title="订单记录" value="#/admin/order"/>
      </mu-list-item>
      <mu-list-item title="设置" toggleNested>
        <mu-list-item slot="nested" title="修改密码" value="#/admin/pass"/>
        <mu-list-item slot="nested" title="支付方式" value="#/admin/pay"/>
        <mu-list-item slot="nested" title="推介系统" value="#/admin/affiliate"/>
      </mu-list-item>
    </mu-list>
  </mu-drawer>
</template>

<script>
  export default {
    props: {
      open: {
        type: Boolean,
        'default': true
      },
      docked: {
        type: Boolean,
        'default': true
      },
      version: {
        'default': '1.0'
      }
    },
    data () {
      return {
        chooseMenu: window.location.hash || '#/index',
        proxyNode: '1' // myFunc.cookie.get('node')
      }
    },
    created () {
      this.routes = this.$router.options.routes
      window.addEventListener('hashchange', () => {
        this.getRedirectionRouter()
        // console.log('title hash change');
      })
      this.getRedirectionRouter()
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
      handleClose () {
        // console.log('handleClose');
        this.$emit('close')  // 需要切换open状态  这个open在父组件里面
      },
      handleMenuIndexClick (event) {
        this.clickedIndexItem = event
      },
      handleMenuChange (val) {
        if (val === '#/index' && this.clickedIndexItem && this.clickedIndexItem.target) {
          let clickedNode = this.clickedIndexItem.target
          if (clickedNode.id === 'proxyNode' || clickedNode.parentNode.id === 'proxyNode' || clickedNode.parentNode.parentNode.id === 'proxyNode') {
            this.clickedIndexItem = null
            return
          }
        }
        this.clickedIndexItem = null
        this.chooseMenu = val
        window.location.hash = this.chooseMenu
        if (!this.docked) { // 如果是手机版 手动关闭
          this.handleClose()
        }
      },
      getRedirectionRouter () {
        let path = window.location.hash
        if (path && path.length > 1) path = path.substring(1)
        for (let i = 0; i < this.routes.length; i++) {
          if (this.routes[i].children) {
            for (let j = 0; j < this.routes[i].children.length; j++) {
              let route = this.routes[i].children[j]
              let routePath = route.path.indexOf('/:') ? route.path.split('/:')[0] : route.path
              if (path.indexOf(routePath) !== -1) {
                this.chooseMenu = '#' + routePath// '#/index'
                this.$emit('titleChange', route.meta.title || '')
                return
              }
            }
          }
        }
      }
    },
    mounted () {
      // console.log(this.chooseMenu); 当前选中的菜单项
    }
  }
</script>

<style lang='less' type="text/less">
  @import "../../node_modules/muse-ui/src/styles/import.less";

  .my-drawer {
    box-shadow: none;
    border-right: 1px solid @borderColor;
  }

  .my-nav-appbar.mu-appbar {
    background-color: @dialogBackgroundColor;
    color: @secondaryTextColor;
  }

  .my-appbar-title {
    color: @secondaryTextColor;
  }

  .my-nav-sub-header {
    padding-left: 34px;
  }

  .node-change {
    height: 16px;
    .mu-dropDown-menu-icon {
      top: -4px;
    }
    .mu-dropDown-menu-text {
      line-height: 16px;
    }
    .mu-dropDown-menu-line {
      display: none;
    }
  }

  .icon-text {
    font-size: 20px;
    display: inline-block;
    width: 24px;
    height: 24px;
    line-height: 24px;
    text-align: center;
  }
</style>
