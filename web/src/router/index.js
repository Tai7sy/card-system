import Vue from 'vue'
import Router from 'vue-router'

import ShopLayout from '../views/shop/Layout'
import Buy from '../views/shop/buy'
import Record from '../views/shop/record'

import AdminLayout from '../views/admin/Layout'
import Dashboard from '../views/admin/dashboard'
import Login from '../views/admin/login'
import Group from '../views/admin/group'
import Good from '../views/admin/good'
import Order from '../views/admin/order'
import Pass from '../views/admin/pass'
import Affiliate from '../views/admin/affiliate'
import Pay from '../views/admin/pay'

Vue.use(Router)
const router = new Router({
  routes: [
    {
      path: '/shop',
      component: ShopLayout,
      redirect: '/shop/buy',
      children: [
        {
          path: '/shop/buy',
          name: 'buy',
          meta: { title: '购买卡密' },
          component: Buy
        },
        {
          path: '/shop/record',
          name: 'record',
          meta: { title: '查询记录' },
          component: Record
        },
        {
          path: '/shop/*',
          redirect: 'buy'
        }
      ]
    },
    {
      path: '/admin',
      component: AdminLayout,
      redirect: '/admin/dashboard',
      children: [
        {
          path: '/admin/dashboard',
          name: 'dashboard',
          meta: { title: '后台管理' },
          component: Dashboard
        },
        {
          path: '/admin/group',
          name: 'group',
          meta: { title: '分类管理' },
          component: Group
        },
        {
          path: '/admin/good',
          name: 'card',
          meta: { title: '商品管理' },
          component: Good
        },
        {
          path: '/admin/order',
          name: 'order',
          meta: { title: '订单记录' },
          component: Order
        },
        {
          path: '/admin/pass',
          name: 'pass',
          meta: { title: '修改密码' },
          component: Pass
        },
        {
          path: '/admin/pay',
          name: 'pay',
          meta: { title: '支付方式' },
          component: Pay
        },
        {
          path: '/admin/affiliate',
          name: 'email',
          meta: { title: '推介系统' },
          component: Affiliate
        },
        {
          path: '/admin/login',
          name: 'login',
          meta: { title: '登录' },
          component: Login
        },
        {
          path: '/admin/*',
          redirect: 'dashboard'
        }
      ]
    },
    {
      path: '*',
      redirect: '/shop'
    }
  ]
})
router.beforeEach((to, from, next) => {
  document.title = to.meta.title
  next()
})

export default router
