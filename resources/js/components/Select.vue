<template>
  <nav class="select-pages-nav" v-if="loginUser && setProfile">
    <div class="select-pages-nav__list">
      <div class="select-pages-nav__item" v-show="groupListBtn">
        <RouterLink class="select-pages-nav__btn" :to="`/mypage/${ loginUser.id }/groups`"><span class="select-pages-nav__text">グループ一覧</span></RouterLink>
      </div>
      <div class="select-pages-nav__item">
        <RouterLink class="select-pages-nav__btn" :to="`/mypage/${ loginUser.id }/groups/create`"><span class="select-pages-nav__text">グループ作成</span></RouterLink>
      </div>
      <div class="select-pages-nav__item">
        <RouterLink class="select-pages-nav__btn" :to="`/mypage/${ loginUser.id }/groups/reserch`"><span class="select-pages-nav__text">グループ参加</span></RouterLink>
      </div>
      <div class="select-pages-nav__item" v-show="checkCurrentPath">
        <button class="select-pages-nav__btn--exit" @click="openModal"><span class="select-pages-nav__text--exit">グループ退会</span></button>
      </div>
    </div>
  </nav>
</template>

<script>

import { OK } from '../util.js'
import { mapState } from 'vuex'

export default {

  computed: {
    ...mapState({
      loginUser: state => state.auth.user,
      setProfile: state => state.profile.myProfile,
      modalStatus: state => state.modal.selectExitGroup
    }),
    //グループ一覧ボタンを表示させるか否かの判定
    groupListBtn() {
      return this.$route.path !== `/mypage/${ this.loginUser.id }/groups`
    },
    //グループ退会ボタンを表示されるか否かの判定
    checkCurrentPath() {
      return this.$route.path === `/mypage/${ this.$route.params['id']}/groups/${ this.$route.params['group']}`
    }
  },
  methods: {
    //モーダルストアへモーダルの内容をセットする
    openModal() {
      this.$store.commit('modal/setModal', {
        modalStatus: true,
        contentText: '本当にグループを退会しますか？',
        buttonText: '退会する',
        modalContent: 'selectExit'
      })
    }
  },
  watch: {
    //グループ退会処理の実行フラグを監視
    modalStatus: {
      async handler(val) {
        if(val) {
          const response = await axios.get(`/api/mypage/${ this.loginUser.id }/groups/${ this.$route.params['group'] }/exit`)
          console.log(response.status)
          if( response.status !== OK ) {
            this.$store.commit('message/setErrorContent', {
              errorContent: "グループの退会に失敗しました",
              timeout: 6000
            })
            this.$store.commit('error/setCode', response.status)
            return false
          }
          this.$store.commit('message/setSuccessContent', {
            successContent: "正常にグループを退会しました",
            timeout: 6000
          })
          this.$router.push(`/mypage/${ this.loginUser.id }/groups`)
          this.$store.commit('modal/setSelectExitGroup', false)
        }
      }
    }
  }
}

</script>
