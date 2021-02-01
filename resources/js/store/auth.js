import { OK, CREATED, UNPROCESSABLE_ENTITY } from '../util.js'

const state = {
  user: null,
  //APIの呼び出しに成功したかどうかをしめすフラグステート
  apiStatus: null,
  //APIを呼び出しの際に発生したエラーメッセージをセットする場所
  loginErrortMessages: null,
  registerErrorMessages: null
}

const getters = {
  //ログイン状態かチェック
  //まず最初の!で、真偽値の逆判定が返る。もうひとつの!で、真の真偽判定が返る
  check: state => !! state.user,
  userid: state => state.user ? state.user.id : null,
  //ユーザーネームがnullの場合は空文字列を返す。それ以外はuserのnameを返す
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
  //第一引数にはミューテーションを呼び出すcommitメソッドなどが入っているcontextオブジェクトを記入
  async register (context, data) {
    context.commit('setApiStatus', null)
    const response = await axios.post('/api/register', data)
    if(response.status === CREATED) {
      // API成功フラグをたてる
      context.commit('setApiStatus', true)
      // ユーザーステートに会員登録したユーザー情報を格納する
      context.commit('setUser', response.data)
      return false
    }
    //失敗の場合、まず失敗フラグをたてる
    context.commit('setApiStatus', false)
    context.commit('message/setErrorContent', {
      errorContent: "会員登録に失敗しました",
      timeout: 6000
    }, { root:true })
    //APIレスポンスが422の場合、ステートのエラーメッセージをセットする
    //バリデーションエラーはそのエラー用のページに遷移させずに、そのページ内でエラーは内容を表示させるだけなので、errorモジュールのステートを更新してApp.vueに検知させる必要はない
    if(response.status === UNPROCESSABLE_ENTITY) {
      context.commit('setRegisterErrorMessages', response.data.errors)
    }
    //それ以外のエラーレスポンスの場合、エラーモジュールのステートにエラーフラグをたてる
    //あるモジュールから別のモジュールのミューテーションを実行する場合、{ root:true }　を追加する
    else {
      //errorモジュールのcodeステートを該当のエラーコードに更新（App.vueがすぐに検知する）
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
    //ログインユーザーの取得をリクエストし、結果を受信
    const response = await axios.get('/api/user')
    //ログインしていない場合は、nullをuserに代入
    const user = response.data || null
    //成功した場合
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
