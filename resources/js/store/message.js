const state = {
  // 成功メッセージ
  successContent: '',
  // 失敗メッセージ
  errorContent: ''
}

const mutations = {
  setSuccessContent (state, { successContent, timeout }) {
    state.successContent = successContent

    if (typeof timeout === 'undefined') {
      timeout = 3000
    }

    //一定時間たったら消えるように設定
    setTimeout(() => (state.successContent = ''), timeout)
  },
  setErrorContent (state, { errorContent, timeout }) {
    state.errorContent = errorContent

    if (typeof timeout === 'undefined') {
      timeout = 3000
    }

    //一定時間たったら消えるように設定
    setTimeout(() => (state.errorContent = ''), timeout)
  }
}

export default {
  namespaced: true,
  state,
  mutations
}
