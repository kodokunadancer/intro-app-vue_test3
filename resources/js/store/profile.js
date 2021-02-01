import { OK, CREATED, UNPROCESSABLE_ENTITY } from '../util.js'

const state = {
  myProfile: null,
  apiStatus: false,
  errorMessages: null
}

const getters = {
  check: state => !! state.myProfile
}

const mutations = {
  setProfile(state, myProfile) {
    state.myProfile = myProfile
  },
  setApiStatus(state, status) {
    state.apiStatus = status
  },
  setErrorMessages(state, messages) {
    state.errorMessages = messages
  }
}

const actions = {

  async createProfile(context, data) {
    const response = await axios.post('/api/profile/create', data)
    if( response.status === CREATED) {
      context.commit('setApiStatus', true)
      context.commit('setProfile', response.data)
      return false
    }
    context.commit('setApiStatus', false)
    if( response.status === UNPROCESSABLE_ENTITY) {
      context.commit('message/setErrorContent', {
        errorContent: "プロフィールの登録に失敗しました",
        timeout: 6000
      }, { root:true })
      context.commit('setErrorMessages', response.data.errors)
      return false
    }
    else {
      context.commit('message/setErrorContent', {
        errorContent: "プロフィールの登録に失敗しました",
        timeout: 6000
      }, { root:true })
      context.commit('error/setCode', response.status, { root: true})
    }
  },
  //ページをリロードしたときにセットしてあるプロフィール情報が破棄されるのを防ぐ
  //profileステートにログインユーザーのプロフィールをセット
  async currentProfile(context) {
    context.commit('setApiStatus', null)
    const response = await axios.get('/api/profile')
    const myProfile = response.data || null
    if( response.status === OK ) {
      context.commit('setApiStatus', true)
      context.commit('setProfile', myProfile)
      return false
    }
    context.commit('setApiStatus', false)
    context.commit('error/setCode', response.status, { root: true} )
  }
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}
