const state =  {
  //モーダルの開閉状態を示す
  modalStatus: false,
  //セレクトバーからのモーダルの開閉状態を示す
  modalSelectStatus: false,
  //以下文章やボタンの文字などモーダルの内容
  contentText: '',
  buttonText: '',
  modalContent: '',
  //以下、コンポーネントで監視し処理へ移させる際のフラグ
  exitGroup:  false,
  //select.vueで監視する用のフラグ（監視するステートが同じだと退会処理がループする）
  selectExitGroup: false,
  naviExitGroup: false,
  deleteGroup: false,
  forceExit: false,
}

// 処理が発火するフラグのオンオフをここで制御
const mutations = {

  setModal(state, {modalStatus, contentText, buttonText, modalContent}) {
    state.modalStatus = modalStatus,
    state.contentText = contentText,
    state.buttonText = buttonText,
    state.modalContent = modalContent
  },
  setExitGroup(state, exitGroup) {
    state.exitGroup = exitGroup
  },
  setSelectExitGroup(state, selectExitGroup) {
    state.selectExitGroup = selectExitGroup
  },
  setNaviExitGroup(state, naviExitGroup) {
    state.naviExitGroup = naviExitGroup
  },
  setDeleteGroup(state, deleteGroup) {
    state.deleteGroup = deleteGroup
  },
  setForceExit(state, forceExit) {
    state.forceExit = forceExit
  }

}
export default {
  namespaced: true,
  state,
  mutations,
}
