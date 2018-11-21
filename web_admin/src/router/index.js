import Vue from 'vue';
import Router from 'vue-router';
/* layout */
import Layout from '../views/layout/Layout.vue';

// const _import = require('./_import_' + process.env.NODE_ENV);
// in development env not use Lazy Loading,
// because Lazy Loading large page will cause webpack hot update too slow.so only in production use Lazy Loading
const _import = file => () => import('views/' + file + '.vue');


Vue.use(Router);

/**
 * icon : the icon show in the sidebar
 * hidden : if `hidden:true` will not show in the sidebar
 * redirect : if `redirect:noredirect` will not redirect in the levelbar
 * noDropdown : if `noDropdown:true` will not has submenu in the sidebar
 * meta : `{ role: ['admin'] }`  will control the page role
 **/
const constantRouterMap = [
  { path: '/admin/login', name: '登录', component: _import('login/login'), hidden: true },
  { path: '/admin/404', name: '404 Not Found', component: _import('404'), hidden: true },
  { path: '/admin/', redirect: '/admin/dashboard/index', hidden: true },
  {
    path: '/admin/dashboard',
    component: Layout,
    redirect: '/admin/dashboard/index', // 用于level-top-bar 的导航
    hidden: false,
    noDropdown: true,
    icon: 'tachometer',
    children: [{ path: 'index', name: '概览', component: _import('dashboard/index') }]
  },
  {
    path: '/admin/system',
    component: Layout,
    redirect: 'noredirect',
    hidden: false,
    icon: 'cogs',
    name: '管理中心',
    children: [
      { path: 'set', name: '网站设置', icon: 'cog', component: _import('system/set/index') },
      { path: 'pay', name: '支付渠道', icon: 'credit-card', component: _import('pay/list/index') },
      { path: 'logs', name: '登录日志', icon: 'sticky-note-o', component: _import('log/list/index') },
      { path: 'security', name: '修改密码', icon: 'shield', component: _import('system/security/index') },
    ]
  },
  {
    path: '/admin/product',
    component: Layout,
    redirect: 'noredirect',
    hidden: false,
    icon: 'cubes',
    name: '商品管理',
    children: [
      { path: 'category', name: '商品分类', icon: 'bars', component: _import('category/list/index') },
      { path: 'list', name: '商品列表', icon: 'bars', component: _import('product/list/index') },
      { path: 'add', name: '添加商品', icon: 'plus', component: _import('product/add/index') },
      { path: 'card/add', name: '添加卡密', icon: 'plus', component: _import('card/add/index') },
      { path: 'card/list', name: '卡密管理', icon: 'bars', component: _import('card/list/index') },
      { path: 'card/recycle', name: '卡密回收站', icon: 'trash-o', component: _import('card/recycle/index') },
    ]
  },
  {
    path: '/admin/order',
    component: Layout,
    redirect: 'noredirect',
    hidden: false,
    icon: 'list-ul',
    name: '交易管理',
    children: [
      { path: 'list', name: '订单记录', icon: 'bars', component: _import('order/list/index') },
      { path: 'recent', name: '7日订单', icon: 'bars', component: _import('order/recent/index') },
      { path: 'analysis', name: '收益统计', icon: 'bar-chart', component: _import('order/profit/index') }
    ]
  },
  {
    path: '/admin/coupon',
    component: Layout,
    redirect: 'noredirect',
    hidden: false,
    icon: 'tags',
    name: '营销助手',
    children: [
      { path: 'add', name: '添加优惠券', icon: 'plus', component: _import('coupon/add/index') },
      { path: 'list', name: '管理优惠券', icon: 'bars', component: _import('coupon/list/index') }
    ]
  }
];

export { constantRouterMap };


export default new Router({
  mode: 'history', // 后端支持可开
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRouterMap
});

export const asyncRouterMap = [
  {
    path: '/admin/about',
    component: Layout,
    redirect: '/admin/about/index',
    hidden: false,
    noDropdown: true,
    icon: 'info',
    children: [{ path: 'index', name: '关于系统', component: _import('about/index') }]
  },
  { path: '/*', redirect: '/admin/404', hidden: true },
];

