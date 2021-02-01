<template>
  <div class="system-error">
    <div class="error-content">
      <h2 class="error-number">500エラー</h2>
      <h3 class="error-message">システムエラー設定が発生しました</h3>
      <button @click="nextPage" class="next-btn">
        {{ status }}
      </button>
   </div>
  </div>
</template>

<script>

export default {

  data() {
    return {
      status: ''
    }
  },
  computed: {
    isLogin() {
      return this.$store.getters['auth/check']
    },
    checkProfile() {
      return this.$store.getters['profile/check']
    }
  },
  methods: {
    fetchStatus() {
      if( this.isLogin && this.checkProfile) {
        this.status = 'グループ一覧ページへ'
      }
      else if (this.isLogin) {
        this.status = 'プロフィール設定ページへ'
      }
      else {
        this.status = 'ログイン/会員登録ページへ'
      }
    },
    nextPage() {
      if( this.isLogin && this.checkProfile) {
        this.$router.push(`/mypage/${ this.$store.getters['auth/userid']}/groups`)
      }
      else if (this.isLogin) {
        this.$router.push('/profile/create')
      }
      else {
        this.$router.push('/login')
      }
    }
  },
  watch: {
    $route: {
      async handler() {
        await this.fetchStatus()
      },
      immediate: true
    }
  }
}

</script>
