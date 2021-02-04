<template>
  <div class="create-group">
    <div class="tittle">グループを新規に作成する</div>
    <!-- グループ作成フォーム -->
    <form class="form" @submit.prevent="submit">
      <!-- エラーメッセージ -->
      <div class="errors" v-if="errors">
        <ul class="errors__list" v-if="errors.name">
          <li class="errors__item" v-for="msg in errors.name" :key="msg">{{ msg }}</li>
        </ul>
      </div>
      <div class="form-group">
        <label class="form-group__label" for="name">グループ名</label>
        <input class="form-group__input" type="text" id="name" name="name" v-model="GroupForm.name" />
      </div>
      <div class="submit">
        <button class="submit__btn" type="submit">送信</button>
      </div>
    </form>
  </div>
</template>

<script>

import { CREATED, UNPROCESSABLE_ENTITY, INTERNAL_SERVER_ERROR } from '../../util.js'

export default {

  props: {
    id: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      GroupForm: {
        name: ''
      },
      errors: null
    }
  },
  methods: {
    //グループ作成処理
    async submit() {
      const response = await axios.post(`/api/mypage/${ this.id }/groups/create`, this.GroupForm)
      if( response.status !== CREATED) {
        this.$store.commit('message/setErrorContent', {
          errorContent: "グループの作成に失敗しました",
          timeout: 6000
        })
        this.$store.commit('error/setCode', response.status)
      }
      if ( response.status === UNPROCESSABLE_ENTITY) {
        this.$store.commit('message/setErrorContent', {
          errorContent: "グループの作成に失敗しました",
          timeout: 6000
        })
        this.errors = response.data.errors
        return false
      }
      this.$router.push(`/mypage/${ this.id }/groups`)
      this.$store.commit('message/setSuccessContent',{
        successContent: 'グループの作成に成功しました',
        timeout: 6000
      })
    }
  }
}

</script>
