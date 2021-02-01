<template>
  <div class="home">
    <nav class="panel" v-if="isLogin && checkProfile">
      <div class="text">プロフィール設定までされているようですね！次はグループ一覧ページへ！</div>
      <div class="next-page">
        <RouterLink
          :to="`/mypage/${this.$store.getters['auth/userid']}/groups`"
        >
        グループ一覧ページへ
        </RouterLink>
      </div>
    </nav>
    <nav class="panel" v-else-if="isLogin">
      <div class="text">会員登録までされているようですね！次はプロフィールの設定を！</div>
      <div class="next-page">
        <RouterLink
          to="/profile/create"
        >
        プロフィールを設定へ
        </RouterLink>
      </div>
    </nav>
    <nav class="panel" v-else>
      <div class="text">
        まだログインをされていないようです。ログインをしましょう！
      </div>
      <div class="next-page">
        <RouterLink
          to="/login"
        >
        ログインページへ
        </RouterLink>
      </div>
    </nav>
  </div>
</template>

<script>

export default {

  computed: {
    isLogin() {
      return this.$store.getters['auth/check']
    },
    checkProfile() {
      return this.$store.getters['profile/check']
    }
  }
}

</script>
