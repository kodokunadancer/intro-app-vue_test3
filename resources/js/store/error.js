//ルートコンポーネントAPP.vueが常にこのerror.jsのステートを監視しており、ステートにエラー内容がセットされたら、App.vueが特定のエラーページに遷移させる
//つまりここは、通常通りページを遷移させるか、エラーページに遷移させるかの判断基準ストア（データ保管場所）になる
const state = {
  code: null
}
const mutations = {
  setCode(state, code) {
    state.code = code
  }
}
export default {
  namespaced: true,
  state,
  mutations,
}
