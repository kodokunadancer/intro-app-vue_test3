//vuexを使用するためにvueをインポート
import Vue from 'vue'
//vuexをインスタンス化し、使用を開始するため
import Vuex from 'vuex'
//authモジュールもストア内に含めるため
import auth from './auth'
import error from './error'
import profile from './profile'
import message from './message'
import modal from './modal'
import group from './group'
import route from './route'

// Vuexを使用可能にする
Vue.use(Vuex)

//storeというストア（データ保持場所）をインスタン化し作成。その中に同じストアだが、authというモジュールを作り、ストア内を分解する
const store = new Vuex.Store({
  modules: {
    auth,
    profile,
    error,
    message,
    modal,
    group,
    route
  }
})

export default store
