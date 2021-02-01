<template>
  <div class="group-detail">
    <!-- ローディング -->
    <div class="loading" v-show="loading">
      <Loader>ロード中・・・</Loader>
    </div>
    <div class="group-detail-contents" v-if="!loading">
      <div class="tittle">「{{ currentGroup.name }}」プロフィール一覧</div>
      <div class="group-password">
        <p class="group-password__text">グループパスワード</p>
        <p class="group-password__password">{{ currentGroup.password }}</p>
      </div>
      <MyProfile
        :key="myProfile.id"
        :myProfile="myProfile"
        :id="id"
        :group="group"
      />
      <div class="profile-wrapper">
        <Profile
          v-for="(profile,index) in profiles"
          v-if="profile.id != myProfile.id"
          :key="profile.id"
          :profile="profile"
          :id="id"
          :group="group"
          :index="index"
          :currentGroup="currentGroup"
          :profiles="profiles"
        />
      </div>
    </div>
  </div>
</template>

<script>

import { OK } from '../../util.js'
import Profile from '../../components/Profile.vue'
import MyProfile from '../../components/MyProfile.vue'
import Loader from '../../components/Loader'

 export default {

   components: {
     Profile,
     MyProfile,
     Loader
   },

   data() {
     return {
       currentGroup: null,
       profiles: [],
       myProfile: null,
       loading: false
     }
   },
   props: {
     id: {
       type: Number,
       required: true
     },
     group: {
       type: Number,
       required: true
     }
   },
   methods: {
     // グループ内プロフィール一覧の取得
     async fetchProfiles() {
       this.loading = true
       const response = await axios.get(`/api/mypage/${ this.id }/groups/${ this.group }`)
       if( response.status !== OK ) {
         this.$store.commit('error/setCode', response.status)
         return false
       }
       this.loading = false
       this.currentGroup = response.data[0],
       this.myProfile = response.data[1],
       this.profiles = response.data[2]
     }
   },
   watch: {
     $route: {
       async handler() {
         await this.fetchProfiles()
       },
      immediate:true
     }
   }
 }

</script>
