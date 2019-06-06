import device from '~/utils/device'

const isMobile = device.isMobile();

const app = {
  state: {
    sidebar: {
      opened: isMobile ? false : !+localStorage.getItem('sidebarStatus')
    },
    theme: 'default',
    livenewsChannels: localStorage.getItem('livenewsChannels') || '[]',
    visitedViews: [],
    isMobile: device.isMobile()
  },
  mutations: {
    TOGGLE_SIDEBAR: state => {
      if (state.sidebar.opened) {
        localStorage.setItem('sidebarStatus', 1);
      } else {
        localStorage.setItem('sidebarStatus', 0);
      }
      state.sidebar.opened = !state.sidebar.opened;
    },
    ADD_VISITED_VIEWS: (state, view) => {
      if (state.visitedViews.some(v => v.path === view.path)) return
      state.visitedViews.push({ name: view.name, path: view.path })
    },
    DEL_VISITED_VIEWS: (state, view) => {
      let index
      for (const [i, v] of state.visitedViews.entries()) {
        if (v.path === view.path) {
          index = i
          break
        }
      }
      state.visitedViews.splice(index, 1)
    }
  },
  actions: {
    ToggleSideBar: ({ commit }) => {
      commit('TOGGLE_SIDEBAR')
    },
    addVisitedViews: ({ commit }, view) => {
      commit('ADD_VISITED_VIEWS', view)
    },
    delVisitedViews: ({ commit }, view) => {
      commit('DEL_VISITED_VIEWS', view)
    }
  }
};

export default app;
