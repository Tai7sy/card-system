import { asyncRouterMap, constantRouterMap } from '@/router/index';

/**
 * 递归过滤异步路由表，返回符合用户角色权限的路由表
 * @param asyncRouterMap
 * @param {Array} permissions
 * @param {String} prefix
 */
function filterAsyncRouter (asyncRouterMap, permissions, prefix = '/') {
  return asyncRouterMap.filter(route => {
    const currentPath = (prefix + route.path).replace(/\/+/g, '/');
    if (route.children && route.children.length) {
      route.children = filterAsyncRouter(route.children, permissions, currentPath + '/');
      return route.children.length > 0
    }
    return permissions.includes(currentPath)
  })
}


const permission = {
  state: {
    routers: constantRouterMap,
    addRouters: []
  },
  mutations: {
    SET_ROUTERS: (state, routers) => {
      state.addRouters = routers;
      state.routers = constantRouterMap.concat(routers);
    }
  },
  actions: {
    GenerateRoutes ({ commit }, data) {
      return new Promise(resolve => {
        let accessedRouters;
        if (data.user.id === 1 || process.env.PROJECT === 'admin_free') {
          console.log('super admin route');
          accessedRouters = asyncRouterMap
        } else {
          console.log('filter route');
          accessedRouters = filterAsyncRouter(asyncRouterMap, data.permissions)
        }

        accessedRouters.push({ path: '/*', redirect: '/admin/404', hidden: true }); // 404 放在最后
        commit('SET_ROUTERS', accessedRouters);
        console.log('SET_ROUTERS OK');
        resolve();
      })
    }
  }
};

export default permission;
