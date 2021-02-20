<template>
  <div class="my-profile">
    <!-- 編集ボタン -->
    <RouterLink
      :to="`/mypage/${ id }/myprofile/edit`"
      class="my-profile__edit-link"
    >
    編集する
    </RouterLink>
    <!-- マイプロフィールパネル -->
    <RouterLink
      :to = "`/mypage/${ id }/groups/${ group }/profiles/${ myProfile.id }`"
      class="my-profile-panel-link"
    >
      <!-- プロフィール写真 -->
      <div class="my-profile__photo">
        <div class="my-profile__photo--true" v-if="myProfile.photos.length">
          <img :src="myProfile.photos[0].url">
        </div>
        <div class="my-profile__photo--false" v-else>
          <img src="https://introduction-app.s3-ap-northeast-1.amazonaws.com/profiles/profile_img.png">
        </div>
      </div>
      <!-- 名前 -->
      <div class="my-profile__name">{{ myProfile.name }}</div>
      <!-- 自己紹介 -->
      <div class="my-profile__introduction">{{ myProfile.introduction }}</div>
    </RouterLink>
  </div>
</template>

<script>

export default {

  props: {
    myProfile: {
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
    }
  },
}

</script>
