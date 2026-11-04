import Vue from 'vue';
import Router from 'vue-router';

import Layout from '@/views/layout/Layout.vue';
import PageAuth from '@/views/auth/auth-index'
import PageLogin from '@/views/auth/auth-login'
import View404 from '@/views/404'
import ViewAbout from '@/views/about'
import ViewPayList from '@/views/pay/pay-index'
import ViewLog from '@/views/log/list'
import ViewCategoryList from '@/views/category/list'
import ViewProductAdd from '@/views/product/add'
import ViewProductList from '@/views/product/list'
import ViewCardAdd from '@/views/card/add'
import ViewCardList from '@/views/card/list'
import ViewCardRecycle from '@/views/card/recycle'
import ViewCouponList from '@/views/coupon/list'
import ViewCouponAdd from '@/views/coupon/add'
import ViewOrderList from '@/views/order/list'
import ViewOrderRecentList from '@/views/order/recent'
import ViewOrderProfitList from '@/views/order/profit'

const _import = file => () => import('@/views/' + file + '.vue');


Vue.use(Router);

/**
 * icon : the icon show in the sidebar
 * hidden : if `hidden:true` will not show in the sidebar
 * redirect : if `redirect:noredirect` will not redirect in the levelbar
 * noDropdown : if `noDropdown:true` will not has submenu in the sidebar
 * meta : `{ role: ['admin'] }`  will control the page role
 **/
const constantRouterMap = [
  {
    path: '/admin/auth', name: '认证', component: PageAuth, hidden: true,
    children: [
      { path: 'login', name: 'auth.login', meta: { title: '后台登录' }, component: PageLogin },
    ]
  },
  { path: '/admin/404', name: '404 Not Found', component: View404, hidden: true },
  { path: '/admin/', redirect: '/admin/dashboard/index', hidden: true },
  {
    path: '/admin/dashboard',
    component: Layout,
    redirect: '/admin/dashboard/index', // 用于level-top-bar 的导航
    hidden: false,
    noDropdown: true,
    icon: 'tachometer-alt',
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
      { path: 'pay', name: '支付渠道', icon: 'credit-card', component: ViewPayList },
      { path: 'logs', name: '登录日志', icon: 'regular/sticky-note', component: ViewLog },
      { path: 'security', name: '修改密码', icon: 'regular/shield-alt', component: _import('system/security/index') },
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
      { path: 'category', name: '商品分类', icon: 'bars', component: ViewCategoryList },
      { path: 'list', name: '商品列表', icon: 'bars', component: ViewProductList},
      { path: 'add', name: '添加商品', icon: 'regular/plus', component: ViewProductAdd },
      { path: 'card/add', name: '添加卡密', icon: 'regular/plus', component: ViewCardAdd },
      { path: 'card/list', name: '卡密管理', icon: 'bars', component: ViewCardList },
      { path: 'card/recycle', name: '卡密回收站', icon: 'light/trash-alt', component: ViewCardRecycle },
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
      { path: 'list', name: '订单记录', icon: 'bars', component: ViewOrderList },
      { path: 'recent', name: '7日订单', icon: 'bars', component: ViewOrderRecentList },
      { path: 'analysis', name: '收益统计', icon: 'chart-bar', component: ViewOrderProfitList }
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
      { path: 'add', name: '添加优惠券', icon: 'regular/plus', component: ViewCouponAdd },
      { path: 'list', name: '管理优惠券', icon: 'bars', component: ViewCouponList }
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
    children: [{ path: 'index', name: '关于系统', component: ViewAbout }]
  },
  { path: '/*', redirect: '/admin/404', hidden: true },
];

