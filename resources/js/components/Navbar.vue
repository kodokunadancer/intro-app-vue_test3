<template>
  <div class="header-wrap">
    <nav class="header">
      <!-- ヘッダー左 -->
      <div class="header-tittle">
        <button @click="home" class="home">自己紹介アプリ</button>
      </div>
      <!-- ヘッダー右 -->
      <div class="header-item">
        <div class="header-item__username" v-if="isLogin">
          <RouterLink
            :to="`/mypage/${loginUser.id}/myprofile`"
          ><span>{{ loginUser.name }}</span>
          </RouterLink>
        </div>
        <button class="logout-button" v-if="isLogin" @click="logout">ログアウト</button>
        <div v-else class="header-item__login">
          <RouterLink to="/login">
            <span>ログイン/会員登録</span>
          </RouterLink>
        </div>
      </div>
      <!-- スマートフォンの際、メニューアイコンを表示 -->
      <div class="header-menu-bar">
        <button
          class="header-menu-bar__inner"
          :class="[ dropdown ? 'menu-bar--on' : 'menu-bar--off']"
          @click="toggleDropdown"
          v-click-outside="hide"
          >
          <b class="icon"></b>
          <b class="icon"></b>
          <b class="icon"></b>
        </button>
      </div>
    </nav>
    <!-- メニューアイコンを押したときに開かれるドロップダウン -->
    <div class="header-dropdown-wrap">
      <div class="header-dropdown" :class="[ dropdown ? 'is-open' : 'is-close']">
        <ul class="header-dropdown__list">
          <li class="header-dropdown__item" v-if="!isLogin" @click="toggleDropdown">
            <RouterLink
              class="header-link"
              to="/login"
            ><span class="header-link__text">ログイン/会員登録</span>
            </RouterLink>
          </li>
          <li class="header-dropdown__item" v-if="isLogin" @click="toggleDropdown">
            <RouterLink
              class="header-link"
              :to="`/mypage/${loginUser.id}/myprofile`"
            ><span>{{ loginUser.name }}</span>
            </RouterLink>
          </li>
          <li class="header-dropdown__item" v-if="isLogin" v-show="groupListBtn" @click="toggleDropdown">
            <RouterLink
              class="header-link"
              :to="`/mypage/${ loginUser.id }/groups`"
            ><span class="header-link__text">グループ一覧</span>
            </RouterLink>
          </li>
          <li class="header-dropdown__item" v-if="isLogin" @click="toggleDropdown">
            <RouterLink
              class="header-link"
              :to="`/mypage/${ loginUser.id }/groups/create`"
            ><span class="header-link__text">グループ作成</span>
            </RouterLink>
          </li>
          <li class="header-dropdown__item" v-if="isLogin" @click="toggleDropdown">
            <RouterLink
              class="header-link"
              :to="`/mypage/${ loginUser.id }/groups/reserch`"
            ><span class="header-link__text">グループ参加</span>
            </RouterLink>
          </li>
          <li class="header-dropdown__item" v-if="isLogin" v-show="currentGroup" @click="toggleDropdown">
            <button class="header-dropdown__btn" @click="openModal">
              グループ退会
            </button>
          </li>
          <li class="header-dropdown__item" v-if="isLogin" @click="toggleDropdown">
            <button
              class="header-link"
              @click="logout"
            ><span class="header-link__text">ログアウト</span>
          </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapGetters } from 'vuex'
import { OK } from '../util.js'
import ClickOutside from 'vue-click-outside'

export default {
  data() {
    return {
      dropdown: null
    }
  },
  computed: {
    isLogin() {
      return this.$store.getters['auth/check']
    },
    checkProfile() {
      return this.$store.getters['profile/check']
    },
    //現在のページがグループ一覧ページであるかチェック
    groupListBtn() {
      return this.$route.path !== `/mypage/${ this.loginUser.id }/groups`
    },
    ...mapState({
      apiStatus: state => state.auth.apiStatus,
      loginUser: state => state.auth.user,
      currentGroup: state => state.group.currentGroup,
      modalStatus: state => state.modal.naviExitGroup
    }),
  },
  methods: {
    //ヘッダー左のアプリ名をクリックしたときの処理
    home() {
      if( this.isLogin && this.checkProfile) {
        this.$router.push(`/mypage/${ this.$store.getters['auth/userid']}/groups`)
      }
      else if (this.isLogin) {
        this.$router.push('/profile/create')
      }
      else {
        this.$router.push('/login')
      }
    },
    async logout () {
      await this.$store.dispatch('auth/logout')
      if(this.apiStatus) {
        this.$router.push('/login')
      }
    },
    //スマホにてメニューボタンをクリックしたときの挙動
    toggleDropdown() {
      this.dropdown =! this.dropdown
    },
    //モーダルの内容をモーダルストアへ送る
    openModal() {
      this.$store.commit('modal/setModal', {
        modalStatus: true,
        contentText: '本当にグループを退会しますか？',
        buttonText: '退会する',
        modalContent: 'naviExit'
      })
    },
    //メニューアイコン以外をクリックした場合、発火
    hide() {
      this.dropdown = false
    }
  },
  watch: {
    //モーダルの退会ボタンを押した際に発火
    modalStatus: {
      async handler(val) {
        if(val) {
          const response = await axios.get(`/api/mypage/${ this.loginUser.id }/groups/${ this.currentGroup.id }/exit`)
          if( response.status !== OK ) {
            this.$store.commit('error/setCode', response.status)
            this.$store.commit('message/setErrorContent', {
              errorContent: "グループの退会に失敗しました",
              timeout: 6000
            })
            return false
          }
          this.$router.push(`/mypage/${ this.loginUser.id }/groups`)
          this.$store.commit('modal/setSelectExitGroup', false)
          this.$store.commit('message/setSuccessContent', {
            successContent: "正常にグループを退会しました",
            timeout: 6000
          })
        }
      }
    }
  },
  //popupItemを使用して、イベントの外側をクリックしないようにする
  mounted () {
    this.popupItem = this.$el
  },
  //directivesオプションを使用することにより、ローカルディレクティブに登録されるため、機能を使うことができる
  directives: {
    ClickOutside
  }
}

</script>
