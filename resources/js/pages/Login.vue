<template>
  <div class="login-regster-form">
    <div class="guest-btn">
      <form class="guest-form" @submit.prevent="guestLogin">
        <div class="guest-submit">
          <button type="submit" class="guest-submit__btn">ゲストログイン</button>
        </div>
      </form>
    </div>
    <!-- エラーメッセージ -->
    <div class="errors" v-if="loginErrors">
      <ul class="errors__list" v-if="loginErrors.email">
        <li class="errors__item" v-for="msg in loginErrors.email" :key="msg">{{ msg }}</li>
      </ul>
      <ul class="errors__list" v-if="loginErrors.password">
        <li class="errors__item" v-for="msg in loginErrors.password" :key="msg">{{ msg }}</li>
      </ul>
    </div>
    <!-- フォーム選択タブ -->
    <ul class="tab">
      <li
        class="tab__item"
        :class="{'tab__item--active': tab === 1 }"
        @click="tab = 1"
      >ログイン</li>
      <li
        class="tab__item"
        :class="{'tab__item--active': tab === 2 }"
        @click="tab = 2"
      >会員登録</li>
    </ul>
    <!-- ログインフォーム -->
    <div class="panel" v-show="tab === 1">
      <form class="form" @submit.prevent="login">
        <!-- 入植欄 -->
        <div class="form-group">
          <label class="form-group__label" for="login-email">Email</label>
          <input type="text" class="form-group__input" id="login-email" v-model="loginForm.email">
        </div>
        <div class="form-group">
          <label class="form-group__label" for="login-password">Password</label>
          <input type="password" class="form-group__input" id="login-password" v-model="loginForm.password">
        </div>
        <!-- ログインボタン -->
        <div class="submit">
          <button type="submit" class="submit__btn">ログイン</button>
        </div>
      </form>
    </div>
    <!-- 会員登録フォーム -->
    <div class="panel" v-show="tab === 2">
      <!-- エラーメッセージ -->
      <div class="errors" v-if="registerErrors">
        <ul class="errors__list" v-if="registerErrors.name">
          <li class="errors__item" v-for="msg in registerErrors.name" :key="msg">{{ msg }}</li>
        </ul>
        <ul class="errors__list" v-if="registerErrors.email">
          <li class="errors__item" v-for="msg in registerErrors.email" :key="msg">{{ msg }}</li>
        </ul>
        <ul class="errors__list" v-if="registerErrors.password">
          <li class="errors__item" v-for="msg in registerErrors.password" :key="msg">{{ msg }}</li>
        </ul>
      </div>
      <!-- 入力欄 -->
      <form class="form" @submit.prevent="register">
        <div class="form-group">
          <label class="form-group__label" for="username">Name</label>
          <input type="text" class="form-group__input" id="username" v-model="registerForm.name">
        </div>
        <div class="form-group">
          <label class="form-group__label" for="email">Email</label>
          <input type="text" class="form-group__input" id="email" v-model="registerForm.email">
        </div>
        <div class="form-group">
          <label class="form-group__label" for="password">Password</label>
          <input type="password" class="form-group__input" id="password" v-model="registerForm.password">
        </div>
        <div class="form-group">
          <label class="form-group__label" for="password-confirmation">Password (confirm)</label>
          <input type="password" class="form-group__input" id="password-confirmation" v-model="registerForm.password_confirmation">
        </div>
        <div class="submit">
          <button type="submit" class="submit__btn">会員登録</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>

import { mapState } from 'vuex'
export default {
  data () {
    return {
      tab: 1,
      loginForm: {
        email: '',
        password: ''
      },
      registerForm: {
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
      },
    }
  },
  computed: mapState({
      apiStatus: state => state.auth.apiStatus,
      loginErrors: state => state.auth.loginErrortMessages,
      registerErrors: state => state.auth.registerErrorMessages,
      errorMessage: state => state.message.errorContent
  }),
  methods: {
    // ログイン
    async login () {
      await this.$store.dispatch('auth/login', this.loginForm)
      await this.$store.dispatch('profile/currentProfile')
      if(this.apiStatus) {
        this.$router.push({ name: 'GroupList', params: {id: this.$store.getters['auth/userid']}})
        this.$store.commit('message/setSuccessContent', {
          successContent: "ログインに成功しました",
          timeout: 6000
        })
      }
    },
    //会員登録
    async register () {
      await this.$store.dispatch('auth/register', this.registerForm)
      if(this.apiStatus) {
        this.$router.push({ name: 'CreateProfile'})
        this.$store.commit('message/setSuccessContent', {
          successContent: "会員登録に成功しました",
          timeout: 6000
        })
      }
    },
    //ゲストログイン
    async guestLogin() {
      this.loginForm.email = 'engineer@email.com'
      this.loginForm.password = 'test1234'
      await this.$store.dispatch('auth/login', this.loginForm)
      await this.$store.dispatch('profile/currentProfile')
      if(this.apiStatus) {
        this.$router.push({ name: 'GroupList', params: {id: this.$store.getters['auth/userid']}})
        this.$store.commit('message/setSuccessContent', {
          successContent: "ゲストユーザー「Engineer」としてログインしました！",
          timeout: 6000
        })
      }
    },
    // エラーメッセージのクリア
    clearError() {
      this.$store.commit('auth/setLoginErrorMessages', null)
      this.$store.commit('auth/setRegisterErrorMessages', null)
    }
  },
  //エラーメッセージが表示されたあと、別のページに移動してまたログインページに戻ってくるとまだエラーメッセージが残っている
  //そこで、Login.vueインスタンスがあらたに生成されるたびに、ストアにセットされてあるエラーメッセージをクリアする
  created() {
    this.clearError()
  },
  //エラーメッセージが表示された場合、スクロールアップする
  watch: {
    errorMessage: {
      handler() {
        window.scrollTo({
          top: 0,
          behavior: "smooth"
        })
      }
    }
  }
}
</script>
