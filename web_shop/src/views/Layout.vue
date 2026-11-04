<template>
<div ref="app" :class="{'s-opacity-75':opacity}">
  <img v-if="opacity" :src="require('@/assets/images/figure_layer_close.png')"
       alt="关闭背景特效" title="关闭背景特效"
       style="position: absolute; top: 0;right: 0;display: inline-block; cursor: pointer"
       @click="removeBackground">
  <header class="gray-background">
    <div class="header">
      <a v-if="config.logo && config.logo.length" href="/" target="_blank">
        <img class="logo" :src="config.logo" :alt="config.name"/>
      </a>
      <a v-else class="header-title" href="/">{{config.name}}</a>
      <div class="top-nav">
        <a class="header-btn" v-if="!config.shop" href="/">首页</a>
        <template v-if="!isMobile">
          <a class="header-btn" v-if="config.shop"
             :class="{active:topNav==='buy'}" @click="handleNavChange('buy')">购买商品</a>
          <a class="header-btn"
             :class="{active:topNav==='record'}" @click="handleNavChange('record')">查询记录</a>
          
        </template>
      </div>
    </div>
  </header>
  <div class="main">
    <!-- 手机端不缓存页面, 为了效率考虑 -->

    <template v-if="isMobile">
      <buy-view v-if="topNav === 'buy'"/>
      <record-view v-if="topNav === 'record'"/>
      
    </template>

    <template v-if="!isMobile">
      <transition name="slide-fade">
        <buy-view v-show="topNav === 'buy'"/>
      </transition>
      <transition name="slide-fade">
        <record-view v-show="topNav === 'record'"/>
      </transition>
    </template>

    <footer>
      <p class="text">
        <a @click="devTest">{{ cp_company }}</a>
        <a  class="link" :href="cp_link" target="_blank" style="float: right">
          <i class="icon iconfont">&#xeee2;</i>
        </a>
      </p>
    </footer>
  </div>
  <mu-bottom-nav ref="bottomNav" v-if="isMobile" :value="bottomNav" @change="handleBottomNavChange"
                 style="position: fixed;bottom: 0;width: 100%;" class="white-background">
    <mu-bottom-nav-item v-if="config.shop" value="buy" title="商品购买" icon="add_shopping_cart"/>
    <mu-bottom-nav-item value="record" title="订单查询" icon="search"/>
    
  </mu-bottom-nav>

</div>

</template>
<script>
import { htmlspecialchars, param } from '~/utils/helper';
import device from '~/utils/device';
import BuyView from '../views/buy/buy-index.vue';
import RecordView from '../views/record/record-index.vue';
let cp_link;
cp_link = 'https://github.com/Tai7sy/card-system';
const cp_company = window.config.company;
export default {
  components: {
    BuyView,
    RecordView
  },
  data() {
    return {
      config: window.config,
      /** @var {boolean} 是否开启透明特效 (有背景) */
      opacity: false,
      isMobile: device.isMobile(),
      topNav: '',
      bottomNav: '',
      cp_link,
      cp_company
    };
  },
  created() {
    const _this = this;
    // lite router

    let routers = [];
    routers = ['buy', 'record'];
    window.$router = {
      nav: '',
      query: {},
      push(nav, query = {}) {
        
        const hash = this.parse(nav, query);
        if (hash === 'buy') {
          // remove '#/buy' from url, 主页不显示锚点
          if (location.hash) {
            history.replaceState(null, null, location.pathname);
          }
        } else {
          window.location.hash = '/' + hash;
        }

        // 到页面顶部
        document.body.scrollTop = 0;
      },
      parse(nav, query = {}) {
        const idx = nav.indexOf('?');
        if (idx !== -1) {
          nav.substring(idx + 1).split('&').forEach(e => {
            const val = e.split('=');
            if (!val.length) return;
            query[val[0]] = val[1];
          });
          nav = nav.substring(0, idx);
        }
        while (nav.substring(0, 1) === '/') {
          nav = nav.substring(1);
        }
        
        const idx2 = routers.indexOf(nav);
        if (idx2 === -1) {
          nav = 'buy';
        }
        this.query = query;
        this.nav = nav;
        _this.topNav = nav;
        _this.bottomNav = nav;
        let title = ['购买商品', '查询记录', '投诉订单'][idx2];
        if (window.config.shop && window.config.shop.name) {
          if (idx2 === 0) {
            title = window.config.shop.name;
          } else {
            title += ' - ' + window.config.shop.name;
          }
        }
        window.document.title = title;
        if (Object.keys(query).length) {
          nav += '?' + param(query);
        }
        return nav;
      }
    };
    window.addEventListener('hashchange', _ => {
      const idx = location.href.indexOf('#');
      let nav = idx !== -1 ? location.href.substring(idx + 1) : 'buy';
      window.$router.parse(nav);
    });
    const idx = location.href.indexOf('#');
    let nav = idx !== -1 ? location.href.substring(idx + 1) : 'buy';
    window.$router.push(nav);
  },
  mounted() {
    if (this.config.shop) {
      if (this.config.theme.show_background && this.config.theme.background && this.config.theme.background.length > 2) {
        const bkg = document.getElementById('bkg');
        bkg.style.height = document.body.clientHeight + 60;
        bkg.style.backgroundImage = `url(${htmlspecialchars(this.config.theme.background)})`;
        if (this.isMobile) {
          bkg.style.backgroundSize = 'auto';
        }
        this.opacity = true;
      }
    }
    window.addEventListener('resize', this.resizeHandler);
  },
  beforeDestroy() {
    window.removeEventListener('resize', this.resizeHandler);
  },
  methods: {
    resizeHandler() {
      if (this.isMobile) {
        /** @type {HTMLElement} */
        const el = this.$refs.bottomNav.$el;
        if (document.documentElement.clientHeight <= window.screen.availHeight * 3 / 4) {
          el.style.position = 'absolute';
          el.style.marginTop = '-.5rem';
          el.style.display = 'none';
        } else {
          el.style.position = 'fixed';
          el.style.display = 'block';
        }
      }
    },
    handleNavChange(val) {
      this.topNav = val;
      this.bottomNav = val;
      window.$router.push(val);
    },
    handleBottomNavChange(val) {
      this.topNav = val;
      this.bottomNav = val;
      window.$router.push(val);
    },
    removeBackground() {
      const element = document.getElementById('bkg');
      element.parentNode.removeChild(element);
      this.opacity = false;
    },
    devTest() {
      this.devCount = this.devCount ? this.devCount + 1 : 1;
      if (this.devCount >= 2) {
        this.removeBackground();
      }
    }
  }
};
</script>

<style lang="scss">

  .main {
    max-width: 1154px;
    margin: 0 auto;
    padding-bottom: 0;
    position: relative;
  }

  header {
    width: 100%;
    box-shadow: 0 3px 9px -4px rgba(0, 0, 0, 0.25);

    .header {
      margin: 0 auto;
      max-width: 1154px;
      width: 100%;
      height: 65px;
      padding: 0 10px;
      font-size: 12px;
      overflow: hidden;

      .header-title {
        white-space: nowrap;
        display: inline-block;
        line-height: 65px;
        font-weight: lighter;
        font-size: 28px;
        color: black;
      }

      .logo {
        margin: 5px 0;
        height: 55px;
        width: 110px;
      }
    }
  }

  .top-nav {
    margin: 24px 0;
    float: right;

    .header-btn {
      font-size: 16px;
      cursor: pointer;
      display: inline-block;
      margin-left: 8px;
      color: black;
      transition: all 0.4s;
      text-decoration: none;
      white-space: nowrap;

      &:hover {
        color: #03a9f4;
      }

      &.active {
        cursor: inherit;
        text-decoration: none;
        color: #03a9f4;
      }
    }
  }

  footer {
    max-width: 1154px;
    width: 100%;
    margin: 50px auto 0;
    height: 24px;
    padding: 0 8px;

    a {
      transition: all 0.4s;
    }
  }

  .content {
    margin: 18px 8px 8px;
  }

  @media only screen and (max-width: 767px) {
    .content {
      margin: 18px 4% 0 !important;
    }
    .header, footer {
      padding: 0 4% !important;;
    }

    footer {
      height: 100px;
    }
  }
</style>
