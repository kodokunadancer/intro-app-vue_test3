const state = {
  currentGroup: null
}

const mutations = {
  setCurrentGroup(state, currentGroup) {
    state.currentGroup = currentGroup
  }
}

export default {
  namespaced: true,
  state,
  mutations,
}
