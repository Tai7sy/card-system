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
    SET_ROUTERS(state, routers) {
      state.addRouters = routers;

      // 合并 children
      routers.forEach(e => {
        let find = false;
        for (let j = 0; j < constantRouterMap.length; j++) {
          if (constantRouterMap[j].path === e.path) {
            console.log('permission.SET_ROUTERS', constantRouterMap[j]);
            if (constantRouterMap[j].children)
              constantRouterMap[j].children.push(...e.children);
            else constantRouterMap[j].children = e.children;
            find = true;
            break;
          }
        }
        if (!find) {
          constantRouterMap.push(e)
        }
      });
      state.routers = constantRouterMap;
    }
  },
  actions: {
    GenerateRoutes ({ commit }, response) {
      return new Promise(resolve => {
        let accessedRouters;
        if (response.user.id === 1 || process.env.PROJECT === 'admin_free') {
          console.log('super admin route');
          accessedRouters = asyncRouterMap
        } else {
          // 管理员, 需要过滤权限
          console.log('filter route');
          accessedRouters = filterAsyncRouter(asyncRouterMap, response.permissions)
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
