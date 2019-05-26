import { login, getInfo, logout } from '@/api/login';
import { getToken, removeToken, setToken } from '~/utils/auth';

const user = {
  state: {
    token: getToken(),
    userInfo: {}, // 用户信息
    role: ''
  },

  mutations: {
    SET_TOKEN: (state, token) => {
      state.token = token;
    },
    SET_USER_INFO: (state, useInfo) => {
      state.userInfo = useInfo;
    },
    SET_ROLE: (state, role) => {
      state.role = role;
    }
  },

  actions: {
    // 登录
    Login ({ commit }, input) {
      return new Promise((resolve, reject) => {
        login(input.email.trim(), input.password, input.remember).then(response => {
          const data = response.data;
          const userInfo = data.user;
          setToken(data.token, input.remember ? { expires: 7 } : null);
          commit('SET_TOKEN', data.token);
          commit('SET_USER_INFO', userInfo);
          commit('SET_ROLE', userInfo.role);
          resolve(data);
        }).catch(error => {
          reject(error);
        });
      });
    },

    // 获取用户信息
    GetInfo ({ commit, state }) {
      return new Promise((resolve, reject) => {
        getInfo(state.token).then(response => {
          const userInfo = response.data.user;
          commit('SET_USER_INFO', userInfo);
          commit('SET_ROLE', userInfo.role);
          resolve(response);
        }).catch(error => {
          reject(error);
        });
      });
    },

    // 登出
    Logout ({ commit, state }) {
      return new Promise((resolve, reject) => {
        logout(state.token).then(() => {
          commit('SET_TOKEN', '');
          commit('SET_ROLE', '');
          removeToken();
          resolve();
        }).catch(() => {
          commit('SET_TOKEN', '');
          commit('SET_ROLE', '');
          removeToken();
          resolve();
        });
      });
    },
  }
};

export default user;
