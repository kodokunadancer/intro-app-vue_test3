<template>
  <div class="profile">
    <!-- プロフィールパネル上部のボタン -->
    <div class="profile-top" v-if="id === currentGroup.author_id">
      <button class="force-btn" @click="modalOpen(index)">強制退会</button>
    </div>
    <!-- プロフィールパネル -->
    <RouterLink
      :to = "`/mypage/${ id }/groups/${ group }/profiles/${ profile.id }`"
      class="profile-panel-link"
    >
      <div class="profile-panel">
        <!-- プロフィール写真 -->
        <div v-if="profile.photos.length > 0" class="profile-panel__photo">
          <img :src="profile.photos[0].url">
        </div>
        <div v-else class="profile-panel__photo">
          <img src="https://introduction-app.s3-ap-northeast-1.amazonaws.com/profiles/profile_img.png">
        </div>
        <!-- プロフィール名 -->
        <div class="profile-panel__name"><span class="profile-name__text">{{ profile.name }}</span></div>
        <!-- 自己紹介 -->
        <div class="profile-panel__introduction">{{ profile.introduction }}</div>
      </div>
    </RouterLink>
  </div>
</template>

<script>
import { OK } from '../util.js'
export default {

  props: {
    profile: {
      type: Object,
      required: true
    },
    id: {
      type: Number,
      required: true
    },
    group: {
      type: Number,
      required: true
    },
    index: {
      type: Number,
      required: true
    },
    profiles: {
      type: Array,
      required: true
    },
    currentGroup: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      clickedProfileNumber: null
    }
  },
  methods: {
    //モーダルストアへモーダルの内容をセットする
    modalOpen(index) {
      this.$store.commit('modal/setModal', {
        modalStatus: true,
        contentText: `本当に${ this.profile.name }さんを強制退会させてもいいですか？`,
        buttonText: '強制退会',
        modalContent: 'forceExit'
      })
      this.clickedProfileNumber = index
    },
    clearProfileNumber() {
      this.clickedProfileNumber = null
    },
    reload() {
      this.$router.go({path: this.$router.currentRoute.path, force: true })
    }
  },
  computed: {
    checkModalStatus() {
      return this.$store.state.modal.modalStatus
    },
    //クリックされたコンポーネントがこのコンポーネントであるか確認
    checkClickedProfileNumber() {
      return this.clickedProfileNumber === this.index
    },
    //モーダル上の決定ボタンが押されたか確認
    forceExit() {
      return this.$store.state.modal.forceExit
    }
  },
  watch: {
    checkModalStatus: {
      handler(val) {
        if(!val) {
          setTimeout(this.clearProfileNumber, 500)
        }
      }
    },
    //強制退出の処理開始フラグを監視
    forceExit: {
      async handler(val) {
        //モーダルにて強制退会ボタンをクリックし,退会させようとしているプロフィールがこのProfileコンポーネントである場合
        if(val && this.checkClickedProfileNumber) {
          const response = await axios.get(`/api/mypage/${ this.id }/groups/${ this.group }/profiles/${ this.profiles[this.clickedProfileNumber].id }/force`)
          if( response.status !== OK ) {
            this.$store.commit('message/setErrorContent', {
              errorContent: "強制退会させることに失敗しました",
              timeout: 6000
            })
            this.$store.commit('error/setCode', response.status)
            return false
          }
          this.reload()
          this.$store.commit('message/setSuccessContent', {
            successContent: "強制退会を完了しました",
            timeout: 6000
          })
        }
      }
    },
    //ページが切り替わった場合、処理開始のフラグをfalseにしておく
    $route() {
      this.$store.commit('modal/setForceExit', false)
    }
  }
}

</script>
