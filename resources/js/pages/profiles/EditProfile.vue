<template>
  <div class="edit-profile">
    <!-- ローディング -->
    <div class="loading" v-show="loading">
      <Loader>ロード中・・・</Loader>
    </div>
    <div class="edit-profile-contents" v-if="!loading">
      <EditProfileForm
      :id="id"
      :myProfile="myProfile"
      />
    </div>
  </div>
</template>

<script>

import EditProfileForm from '../../components/EditProfileForm.vue'
import { OK } from '../../util.js'
import Loader from '../../components/Loader'

export default {

  components: {
    EditProfileForm,
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
      loading: false
    }
  },
  methods: {
    //自分のプロフィールの取得
    async getMyProfile() {
      this.loading = true
      const response = await axios.get('/api/profile')
      if( response.status !== OK ) {
        this.$store.commit('error/setCode', response.status)
        return false
      }
      this.loading = false
      this.myProfile = response.data
    }
  },
  watch: {
    $route: {
      async handler() {
        await this.getMyProfile()
      },
      immediate: true
    }
  },
  beforeRouteEnter(to, from, next) {
    next(vm =>{
      vm.$store.commit('route/setPrevRoute', from)
    })
  }
}

</script>
