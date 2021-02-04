import { OK, CREATED, UNPROCESSABLE_ENTITY } from '../util.js'

const state = {
  user: null,
  apiStatus: null,
  loginErrortMessages: null,
  registerErrorMessages: null
}

const getters = {
  check: state => !! state.user,
  userid: state => state.user ? state.user.id : null,
  username: state => state.user ? state.user.name : ''
}

const mutations = {
  setUser (state, user) {
    state.user = user
  },
  setApiStatus(state, status) {
    state.apiStatus = status
  },
  setLoginErrorMessages(state, messages) {
    state.loginErrortMessages = messages
  },
  setRegisterErrorMessages(state, messages) {
    state.registerErrorMessages = messages
  }
}

const actions = {

  async register (context, data) {
    context.commit('setApiStatus', null)
    const response = await axios.post('/api/register', data)
    if(response.status === CREATED) {
      context.commit('setApiStatus', true)
      context.commit('setUser', response.data)
      return false
    }
    context.commit('setApiStatus', false)
    context.commit('message/setErrorContent', {
      errorContent: "会員登録に失敗しました",
      timeout: 6000
    }, { root:true })
    if(response.status === UNPROCESSABLE_ENTITY) {
      context.commit('setRegisterErrorMessages', response.data.errors)
    }
    else {
      context.commit('error/setCode', response.status, { root:true })
    }
  },

  async login (context, data) {
    context.commit('setApiStatus',null)
    const response = await axios.post('/api/login', data)
    if(response.status === OK) {
      context.commit('setApiStatus', true)
      context.commit('setUser', response.data)
      return false
    }
    context.commit('message/setErrorContent', {
      errorContent: "ログインに失敗しました",
      timeout: 6000
    }, { root:true })
    context.commit('setApiStatus', false)
    if(response.status === UNPROCESSABLE_ENTITY) {
      context.commit('setLoginErrorMessages',response.data.errors)
    }
    else {
      context.commit('error/setCode', response.status, { root:true })
    }
  },

  async logout (context) {
    const response = await axios.post('/api/logout')
    if(response.status === OK) {
      context.commit('setApiStatus', true)
      context.commit('setUser', null)
      //プロフィールをnullにしておかなければ、新たにログインしても先程のプロフィールが残っているため、プロフィール作成ページへの遷移ができない（ナビゲーションガードに引っかかる）
      context.commit('profile/setProfile', null, { root:true })
      context.commit('message/setSuccessContent', {
        successContent: '正常にログアウトできました',
        timeout: 6000
      }, { root:true })
      return false
    }
    context.commit('message/setErrorContent', {
      errorContent: "ログアウトに失敗しました",
      timeout: 6000
    }, { root:true })
    contet.commit('setApiStatus', false)
    context.commit('error/setCode', response.status, { root:true })
  },

  //ページをリロードしたときにセットしてあるユーザー情報が破棄されるのを防ぐ
  //userステートにログインユーザーをセット
  async currentUser(context) {
    context.commit('setApiStatus', null)
    const response = await axios.get('/api/user')
    const user = response.data || null
    if(response.status === OK) {
      context.commit('setApiStatus', true)
      context.commit('setUser', user)
      return false
    }
    context.commit('setApiStatus', false)
    context.commit('error/setCode', response.status, { root:true })
  },
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}
