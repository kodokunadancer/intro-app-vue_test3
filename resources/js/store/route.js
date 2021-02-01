//前のページの情報を保管する
const state = {
  prevRoute: null
}

const mutations = {
  setPrevRoute(state, prevRoute) {
    state.prevRoute = prevRoute
  }
}

export default {
  namespaced: true,
  state,
  mutations,
}
