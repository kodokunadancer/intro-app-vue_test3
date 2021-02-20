<template>
  <div class="show-profile">
    <!-- ローディング -->
    <div class="loading" v-show="loading">
      <Loader>ロード中・・・</Loader>
    </div>
    <!-- プロフィール -->
    <div class="show-profile-contents" v-if="!loading">
      <div class="tittle">{{ myProfile.name }}さんのマイプロフィール</div>
      <RouterLink
        :to="`/mypage/${ id }/myprofile/edit`"
        class="profile__edit-link"
      >
        <span class="edit-link-text">編集</span>
      </RouterLink>
      <!-- 名前 -->
      <div class="profile-name">{{ myProfile.name }}</div>
      <!-- プロフィール写真 -->
      <div class="profile-photo">
        <div class="profile-photo__item" v-if="myProfile.photos.length > 0">
          <img :src="myProfile.photos[0].url">
        </div>
        <div class="profile-photo__item" v-else>
          <img src="https://introduction-app.s3-ap-northeast-1.amazonaws.com/profiles/profile_img.png">
        </div>
      </div>
      <!-- 自己紹介 -->
      <div class="profile-introduction">{{ myProfile.introduction }}</div>
      <!-- コメント一覧 -->
      <div class="comment-tittle">コメント</div>
      <ul class="comment-list" v-if="comments.length > 0">
        <li
          v-for="comment in comments"
          :key="comment.content"
          class="comment-item"
        >
          <p class="comment-item__name">
              {{ comment.author.name }}
          </p>
          <p class="comment-item__content">{{ comment.content }}</p>
          <!-- いいねボタン -->
          <div class="like-btn">
            <span class="like-btn__text">{{ comment.likes_count }}</span>
            <button
              class="like-icon"
              @click="onLikeClick(comment)"
              :class="{ 'heart-animation': comment.liked_by_user }"
            >
            </button>
          </div>
        </li>
      </ul>
      <p class="no-comment" v-else>まだコメントがありません</p>
      <!-- コメント入力欄 -->
      <form class="comment-form" v-if="isLogin" @submit.prevent="addComment">
        <!-- エラーメッセージ -->
        <div class="errors" v-if="commentErrors">
          <ul class="errors-list" v-if="commentErrors.content">
            <li class="errors-item" v-for="msg in commentErrors.content" :key="msg">{{ msg }}</li>
          </ul>
        </div>
        <textarea class="form__textarea" v-model="commentContent"></textarea>
        <div class="submit">
          <button class="submit__btn" type="submit">送信</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>

import { OK, CREATED, UNPROCESSABLE_ENTITY} from '../../util.js'
import Loader from '../../components/Loader'
export default {

  components: {
    Loader
  },
  props: {
    id: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      myProfile: null,
      commentErrors: null,
      commentContent: '',
      comments: [],
      loading: false,
    }
  },
  computed: {
    isLogin() {
      return this.$store.getters['auth/check']
    }
  },
  methods: {
    //自分のプロフィールの表示
    async fetchShowMyProfile() {
      this.loading = true
      const response = await axios.get(`/api/mypage/${this.id}/myprofile`)
      if(response.status !== OK) {
        this.$store.commit('error/setCode', response.status)
        return false
      }
      this.loading = false
      this.myProfile = response.data
      this.comments = response.data.comments
    },
    //コメント投稿処理
    async addComment() {
      const response = await axios.post(`/api/mypage/${this.id}/myprofile/comments`, {
        content: this.commentContent
      })
      if(response.status === UNPROCESSABLE_ENTITY) {
        this.commentErrors = response.data.errors
        return false
      }
      this.commentContent = ''
      this.commentErrors = null

      if(response.status !== CREATED) {
        this.$store.commit('message/setErrorContent', {
          errorContent: "コメントの投稿に失敗しました",
          timeout: 6000
        })
        this.$store.commit('error/setCode', response.status)
        return false
      }
      this.$store.commit('message/setSuccessContent',{
        successContent: 'コメントの投稿に成功しました',
        timeout: 6000
      })
      this.comments = response.data
    },
    // いいね付与したとき、そのコメントにすでにいいねを付与したか否かで処理を分岐する
    // 引数にcommentを受け取ることで、いいねを押したコメントのみを対象に処理を進めることができる
    onLikeClick(comment) {
      if(comment.liked_by_user) {
        this.unlike(comment)
      }
      else {
        this.like(comment)
      }
    },
    //いいね付与
    async like(comment) {
      const response = await axios.put(`/api/mypage/${this.id}/myprofile/comments/${comment.id}`)
      if(response.status !== OK) {
        this.$store.commit('message/setErrorContent', {
          errorContent: "いいね付与に失敗しました",
          timeout: 6000
        })
        this.$store.commit('error/setCode', response.status)
        return false
      }
      this.$store.commit('message/setSuccessContent', {
        successContent: `${ comment.author.name }さんのコメントにいいねしました`,
        timeout: 6000
      })
      comment.likes_count = comment.likes_count + 1
      // 該当のコメントにはいいねをすでに押したことを記録
      comment.liked_by_user = true
    },
    //いいね解除
    async unlike(comment) {
      const response = await axios.delete(`/api/mypage/${this.id}/myprfile/comments/${comment.id}`)
      if(response.status !== OK) {
        this.$store.commit('message/setErrorContent', {
          errorContent: "いいね解除に失敗しました",
          timeout: 6000
        })
        this.$store.commit('error/setCode', response.status)
        return false
      }
      this.$store.commit('message/setSuccessContent', {
        successContent: `${ comment.author.name }さんのコメントからいいねを外しました`,
        timeout: 6000
      })
      comment.likes_count = comment.likes_count - 1
      comment.liked_by_user = false
    }
  },
  watch: {
    $route: {
      async handler() {
        await this.fetchShowMyProfile()
      },
      immediate: true
    }
  }
}

</script>
