<template>
  <div class="reserch-group">
    <div class="tittle">グループ検索</div>
    <div class="reserch" v-if="!group">
      <!-- グループ検索フォーム -->
      <form class="form" @submit.prevent="reserch">
        <!-- エラーメッセージ -->
        <div class="errors" v-if="errors">
          <ul class="errors__list" v-if="errors.group_name">
            <li class="errors__item" v-for="msg in errors.group_name" :key="msg">{{ msg }}</li>
          </ul>
          <ul class="errors__list" v-if="errors.password">
            <li class="errors__item" v-for="msg in errors.password" :key="msg">{{ msg }}</li>
          </ul>
        </div>
        <div class="errors" v-if="reserchError">
          <ul class="errors__list">
            <li class="errors__item">{{ reserchError }}</li>
          </ul>
        </div>
        <!-- 入力フォーム -->
        <div class="form-group">
          <label class="form-group__label" for="group_name">グループ名</label>
          <input class="form-group__input" type="text" id="group_name" name="group_name" v-model="reserchForm.group_name" />
        </div>
        <div class="form-group">
          <label class="form-group__label" for="password">パスワード</label>
          <input class="form-group__input" type="text" id="password" name="password" v-model="reserchForm.password" />
        </div>
        <!-- 送信 -->
        <div class="submit">
          <button class="submit__btn" type="submit">送信</button>
        </div>
      </form>
    </div>
    <!-- グループ検索結果 -->
    <div class="group-info" v-else>
      <div>
        <p>以下のグループでお間違いないですか？<br>正しければ下の完了ボタンを押していただくことでグループに参加することができます。</p>
      </div>
      <!-- グループ 画像-->
      <div class="group-info__photo" v-if="group.photo">
        <img :src="group.photo.url">
      </div>
      <div class="group-info__photo" v-else>
        <img src="https://introduction-app.s3-ap-northeast-1.amazonaws.com/groups/group_img.png">
      </div>
      <!-- グループ名 -->
      <div class="group-info__name">{{ group.name }}</div>
      <div class="submit" @click="join">
        <button class="submit__btn" type="submit">完了</button>
      </div>
    </div>
  </div>
</template>

<script>

import { OK, CREATED, BAD_REQUEST, UNPROCESSABLE_ENTITY, INTERNAL_SERVER_ERROR } from '../../util.js'

export default {

  props: {
    id: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      group: null,
      reserchForm: {
        group_name: '',
        password: ''
      },
      errors: null,
      reserchError: null
    }
  },
  methods: {
    //グループ検索処理
    async reserch() {
      const response = await axios.post(`/api/mypage/${ this.id }/groups/reserch`, this.reserchForm)
      if( response.status === UNPROCESSABLE_ENTITY) {
        this.errors = response.data.errors
        return false
      }
      if( response.status === BAD_REQUEST ) {
        if (response.data['error'] === 'NotGroup') {
          return this.reserchError = 'グループ名とパスワードが一致するグループを見つけられませんでした'
        }
        else if( response.data['error'] === 'Participated') {
          return this.reserchError = '検索したグループにはすでに参加されています。'
        }
      }
      if( response.status !== OK) {
        this.$store.commit('error/setCode', response.status)
        return false
      }
      this.group = response.data
    },
    //グループ参加処理
    async join() {
      const response = await axios.get(`/api/mypage/${ this.id }/groups/${ this.group.id }/join`)
      if( response.status !== CREATED) {
        this.$store.commit('message/setErrorContent', {
          errorContent: "グループの参加に失敗しました",
          timeout: 6000
        })
        this.$store.commit('error/setCode', response.status)
        return false
      }
      this.$router.push(`/mypage/${ this.id }/groups`)
      this.$store.commit('message/setSuccessContent', {
        successContent: `${ this.group.name }の参加に成功しました`,
        timeout: 6000
      })
    }
  }
}

</script>
