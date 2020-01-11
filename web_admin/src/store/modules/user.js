import { login, getInfo, logout } from '@/api/auth';
import { getToken, removeToken, setToken } from '~/utils/auth';

const user = {
  state: {
    token: getToken(),
    userInfo: {}, // 用户信息
  },

  mutations: {
    SET_TOKEN(state, token) {
      state.token = token;
      if (!token) {
        removeToken();
      } else {
        setToken(token, state.remember)
      }
    },
    SET_REMEMBER(state, remember) {
      state.remember = remember;
    },
    SET_USER_INFO(state, useInfo) {
      state.userInfo = useInfo;
    },
  },

  actions: {
    // 登录
    Login ({ commit }, input) {
      return new Promise((resolve, reject) => {
        commit('SET_REMEMBER', input.remember);
        login(input).then(response => {
          if (response.redirect) {
            location.href = response.redirect;
          }
          commit('SET_USER_INFO', response.user);
          resolve(response);
        }).catch(error => {
          reject(error);
        });
      });
    },

    // 获取用户信息
    GetInfo ({ commit, state }) {
      return new Promise((resolve, reject) => {
        getInfo().then(response => {
          commit('SET_USER_INFO', response.user);
          resolve(response);
        }).catch(error => {
          reject(error);
        });
      });
    },

    // 登出
    Logout ({ commit, state }) {
      return new Promise((resolve) => {
        logout().finally(() => {
          commit('SET_USER_INFO', {});
          commit('SET_TOKEN', null);
          resolve();
        });
      });
    },
  }
};

export default user;
