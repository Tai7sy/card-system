import { asyncRouterMap, constantRouterMap } from '@/router/index';

/**
 * 通过meta.role判断是否与当前用户权限匹配
 * @param roles
 * @param route
 */
function hasPermission (roles, route) {
  if (route.meta && route.meta.role) {
    return roles.some(role => route.meta.role.indexOf(role) > -1)
  } else {
    return true
  }
}

/**
 * 递归过滤异步路由表，返回符合用户角色权限的路由表
 * @param asyncRouterMap
 * @param roles
 */
function filterAsyncRouter (asyncRouterMap, roles) {
  return asyncRouterMap.filter(route => {
    if (hasPermission(roles, route)) {
      if (route.children && route.children.length) {
        route.children = filterAsyncRouter(route.children, roles)
      }
      return true
    }
    return false
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
      const user = data.user;
      return new Promise(resolve => {
        let accessedRouters;
        if (user.role.indexOf('admin') > -1) {
          console.log('admin route');
          accessedRouters = asyncRouterMap
        } else {
          console.log('filter route');
          accessedRouters = filterAsyncRouter(asyncRouterMap, user.roles)
        }
        commit('SET_ROUTERS', accessedRouters);
        console.log('SET_ROUTERS OK');
        resolve();
      })
    }
  }
};

export default permission;
