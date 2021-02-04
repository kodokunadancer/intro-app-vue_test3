<template>
  <div class="create-profile">
    <div class="create-profile__tittle">プロフィールを新規に作成する</div>
    <!-- プロファイル作成ページ -->
    <form class="form" @submit.prevent="submit">
      <!-- エラーメッセージ -->
      <div class="errors" v-if="errorMessages">
        <ul class="errors__list" v-if="errorMessages.name">
          <li class="errors__item" v-for="msg in errorMessages.name" :key="msg">{{ msg }}</li>
        </ul>
        <ul class="errors__list" v-if="errorMessages.introduction">
          <li class="errors__item" v-for="msg in errorMessages.introduction" :key="msg">{{ msg }}</li>
        </ul>
      </div>
      <div class="form-group">
        <label class="form-group__label" for="name">名前</label>
        <input class="form-group__input" type="text" id="name" name="name" v-model="profileForm.name" />
      </div>
      <div class="form-group">
        <label class="form-group__label" for="introduction">自己紹介</label>
        <textarea class="form-group__textarea" name="introduction" id="introduction" v-model="profileForm.introduction"></textarea>
      </div>
      <div class="submit">
        <button class="submit__btn" type="submit">送信</button>
      </div>
    </form>
  </div>
</template>

<script>

  import { mapState } from 'vuex'

  export default {

    data() {
      return {
        profileForm: {
          name: '',
          introduction: ''
        }
      }
    },
    computed: mapState({
      myProfile: state => state.profile.myProfile,
      errorMessages: state => state.profile.errorMessages
    }),
    methods: {
      async submit() {
        await this.$store.dispatch('profile/createProfile', this.profileForm)
        if(this.myProfile) {
          this.$router.push(`/mypage/${ this.$store.getters['auth/userid']}/groups`)
          this.$store.commit('message/setSuccessContent', {
            successContent: "プロフィールの登録に成功しました",
            timeout: 6000
          })
        }
      }
    }
  }
</script>
