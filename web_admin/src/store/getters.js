const getters = {
  sidebar: state => state.app.sidebar,
  userInfo: state => state.user.userInfo,
  role: state => state.user.userInfo.role,
  permission_routers: state => state.permission.routers,
  addRouters: state => state.permission.addRouters
};
export default getters
